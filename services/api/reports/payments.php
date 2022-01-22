<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');


include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/PaymentDetail.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $payment_model = new PaymentDetail($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $payment_model->readPaymentsAndUsers($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // payments array
        $payment_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            $payment = copyArray($row, []);

            // Push to "data"
            array_push($payment_arr, $payment);
        }

        // Turn to JSON & output
        echo json_encode($payment_arr);
    } else {
        // No payments
        echo json_encode(
            array()
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
