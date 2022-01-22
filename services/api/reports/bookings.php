<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');


include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/User/Order/Booking.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $booking_model = new Booking($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $booking_model->readBookingsAndPayment($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // bookings array
        $booking_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            $booking = copyArray($row, []);

            // Push to "data"
            array_push($booking_arr, $booking);
        }

        // Turn to JSON & output
        echo json_encode($booking_arr);
    } else {
        // No bookings
        echo json_encode(
            array()
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
