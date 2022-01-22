<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../../common/APIUtil.php';
include_once __DIR__ . '/../../../models/User/Order/Booking.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $booking_model = new Booking($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $booking_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // Users array
        $booking_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
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

        message_logger("Booking data fetched.");
        // Turn to JSON & output
        echo json_encode($booking_arr);
    } else {
        // No Users
        echo json_encode(
            array()
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
