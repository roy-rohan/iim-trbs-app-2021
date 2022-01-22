<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../../common/APIUtil.php';
include_once __DIR__ . '/../../../models/User/Order/Order.php';
include_once __DIR__ . '/../../../models/User/Order/Booking.php';
include_once __DIR__ . '/../../../models/PaymentDetail.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "GET") {

    $order_model = new Order($db);

    // Get raw posted data
    $order_model->order_id = isset($_GET['id']) ? $_GET['id'] : die();

    if ($order_model->read_single()) {
        $booking_model = new Booking($db);
        $bookingQuery = (object)array("filters" => array((object)array("field_name" => 'order_id', "value" => $order_model->order_id, "op" => "=")), "filter_op" => "AND", "sort" => array());
        $bookingResult = $booking_model->read($bookingQuery);
        // Get row count
        $bookingCount = $bookingResult->rowCount();
        $booking_arr = array();
        if ($bookingCount > 0) {
            // Users array

            while ($row = $bookingResult->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $booking = array(
                    'booking_id' => $booking_id,
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'order_id' => $order_id,
                    'product_name' => $product_name,
                    'product_type' => $product_type,
                    'product_image' => $product_image,
                    'status' => $status,
                    'ticket_no' => $ticket_no,
                    'venue' => $venue,
                    'time' => $time,
                    'user_id' => $user_id,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                );

                // Push to "data"
                array_push($booking_arr, $booking);
            }
        }

        $payment = (object)array(
            "payment_id" => $order_model->payment_id,
            "transaction_id" => $order_model->transaction_id,
            "status" => $order_model->payment_status,
            "mode" => $order_model->mode,
            "amount" => $order_model->amount,
            "description" => $order_model->description,
            "created_at" => $order_model->payment_created_at,
            "updated_at" => $order_model->payment_updated_at,
        );

        $order = array(
            "order_id" => $order_model->order_id,
            "total" => $order_model->total,
            "discount" => $order_model->discount,
            "user_id" => $order_model->user_id,
            "coupon_id" => $order_model->coupon_id,
            "taxes" => $order_model->taxes,
            "payment" => $payment,
            "status" => $order_model->status,
            "bookings" => $booking_arr,
            "created_at" => $order_model->created_at,
            "updated_at" => $order_model->updated_at,
        );

        // Turn to JSON & output
        echo json_encode($order);
    } else {
        // No cart
        $exp = new CustomException("No order found.");
        $exp->sendBadRequest();
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
