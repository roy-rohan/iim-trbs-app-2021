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

    // Instantiate coupon_master object
    $coupon_master = new CouponMaster($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $coupon_master->status = $data->status;
    $coupon_master->discount = $data->discount;
    $volume = (int)$data->volume;

    // Create coupon_master
    for ($count = 0; $count < $volume; $count++) {
        $new_coupon_id = $coupon_master->create();
        if (!$new_coupon_id) {
            $exp = new CustomException('Failed to Create coupon: ' . $count);
            $exp->sendBadRequest();
            exit(1);
        }
    }
    echo json_encode(
        array('message' => 'Coupons created successfully: ' . $count)
    );
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
