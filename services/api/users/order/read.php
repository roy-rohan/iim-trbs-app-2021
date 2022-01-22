<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../../common/APIUtil.php';
include_once __DIR__ . '/../../../models/User/Order/Order.php';
include_once __DIR__ . '/../../../models/PaymentDetail.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $order_model = new Order($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));


    $result = $order_model->read($data);

    // Get row count
    $num = $result->rowCount();
    // Check if any order exists
    if ($num > 0) {
        $order_items = array();
        while ($orderResultRow = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($orderResultRow);
            $payment = (object)array(
                "payment_id" => $payment_id,
                "transaction_id" => $transaction_id,
                "status" => $payment_status,
                "mode" => $mode,
                "amount" => $amount,
                "description" => $description,
                "created_at" => $payment_created_at,
                "updated_at" => $payment_updated_at,
            );

            $order = array(
                "order_id" => $order_id,
                "total" => $total,
                "discount" => $discount,
                "user_id" => $user_id,
                "coupon_id" => $coupon_id,
                "taxes" => $taxes,
                "payment" => $payment,
                "status" => $status,
                "created_at" => $created_at,
                "updated_at" => $updated_at,
            );

            array_push($order_items, $order);
            echo json_encode($order_items);
        }
    } else {
        echo json_encode(array());
    }
    // Turn to JSON & output
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
