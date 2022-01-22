<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../../common/APIUtil.php';
include_once __DIR__ . '/../../../models/User/Order/Order.php';
include_once __DIR__ . '/../../../models/Admin/CouponMaster.php';
include_once __DIR__ . '/../../../models/User/Order/Booking.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $order_model = new Order($db);
    $coupon_model = new CouponMaster($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    if ($data->coupon_id) {
        $coupon_model->coupon_id = $data->coupon_id;
        $coupon_model->read_single();
        if (!$coupon_model->status) {
            $coupon_model->status = 1;
            $coupon_model->user_id = $data->user_id;
            $coupon_model->applied_on = date("Y-m-d H:i:s");
            $coupon_model->update();
            $order_model->coupon_id = $data->coupon_id;
        } else {
            $exp = new CustomException("Coupon has expired.");
            $exp->sendBadRequest();
        }
    } else {
        $order_model->coupon_id = null;
    }

    $order_model->total = $data->total;
    $order_model->taxes = $data->taxes;
    $order_model->discount = $data->discount;
    $order_model->user_id = $data->user_id;
    $order_model->status = $data->status;

    $order_id = $order_model->create();

    if ($order_id) {
        // create bookings
        $bookings_data = $data->bookings;
        foreach ($bookings_data as $booking_data) {
            $booking_model = new Booking($db);
            $booking_model->product_id = $booking_data->product_id;
            $booking_model->quantity = $booking_data->quantity;
            $booking_model->price = $booking_data->price;
            $booking_model->order_id = $order_id;
            $booking_model->product_name = $booking_data->product_name;
            $booking_model->product_type = $booking_data->product_type;
            $booking_model->product_image = $booking_data->product_image;
            $booking_model->status = $booking_data->status;
            $booking_model->ticket_no = $booking_data->status == $STATUS_PENDING ? null : uniqid("TRBS");
            $booking_model->venue = $booking_data->venue;
            $booking_model->time = $booking_data->time;
            $booking_model->user_id = $data->user_id;

            $booking_id = $booking_model->create();
            if (!$booking_id) {
                $exp = new CustomException("Order was not added.");
                $exp->sendBadRequest();
                exit(1);
            }
        }
        echo json_encode(
            array(
                'message' => "Order has been added successfully.",
                'order_id' => $order_id
            )
        );
    } else {
        $exp = new CustomException("Order was not added.");
        $exp->sendBadRequest();
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
