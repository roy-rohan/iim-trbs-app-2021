<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/PaymentDetail.php';
include_once __DIR__ . '/../../models/User/Order/Order.php';
include_once __DIR__ . '/../../models/User/Order/Booking.php';


// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $payment_model = new PaymentDetail($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    try {
        $order_model = new Order($db);
        $order_model->order_id = $data->order_id;
        $order_model->read_single();

        $payment_model->transaction_id = $data->transaction_id;
        if ($payment_model->checkDuplicateTransactionId()) {
            $exp = new CustomException("Transaction Id has already been used.");
            $exp->sendBadRequest();
            exit(1);
        }

        // do any taxes calculation if required.
        $payment_model->amount = $order_model->total;

        $payment_model->mode = $data->mode;
        $payment_model->user_id = $data->user_id;
        $payment_model->order_id = $data->order_id;
        $payment_model->status = $data->status;
        $payment_model->description = $data->description;

        $payment_id = $payment_model->create();

        if ($payment_id) {
            // update order status and payment id 
            if ($order_model->status) {
                $order_model->status = $data->status == 1 ? "Success" : "Failed";
                $order_model->update();
            } else {
                $exp = new CustomException("Order could not be updated.");
                $exp->sendBadRequest();
            }
            // update bookings related to order
            $booking_model = new Booking($db);
            $bookingResult = $booking_model->read((object)array("filters" => array((object)array("field_name" => 'order_id', "value" => $order_model->order_id, "op" => "=")), "filter_op" => "AND", "sort" => array()));
            while ($bookingItemRow = $bookingResult->fetch(PDO::FETCH_ASSOC)) {
                $booking_model->booking_id = $bookingItemRow['booking_id'];
                $booking_model->read_single();
                if ($booking_model->status != null) {
                    $booking_model->status =
                        $data->status == 1 ? "Success" : "Failed";
                    $booking_model->ticket_no = uniqid("TRBS");
                    $booking_model->update();
                }
            }

            echo json_encode(
                array('message' => 'Payment details have been saved successfully.')
            );
        } else {
            $exp = new CustomException("Payment details could not be saved.");
            $exp->sendBadRequest();
        }
    } catch (Exception $exp) {
        message_logger($exp);
        $exp = new CustomException("Something went wrong.");
        $exp->sendServerException();
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
