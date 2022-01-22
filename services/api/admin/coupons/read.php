<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../../common/APIUtil.php';
include_once __DIR__ . '/../../../models/Admin/CouponMaster.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $coupon_master_model = new CouponMaster($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $coupon_master_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // Users array
        $coupon_master_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $coupon = array(
                'coupon_id' => $coupon_id,
                'coupon_code' => $coupon_code,
                'status' => $status,
                'discount' => $discount,
                'applied_on' => $applied_on,
                'created_at' => $created_at,
                'updated_at' => $updated_at
            );

            // Push to "data"
            array_push($coupon_master_arr, $coupon);
        }

        message_logger("Coupon data fetched.");
        // Turn to JSON & output
        echo json_encode($coupon_master_arr);
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
