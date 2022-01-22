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

    // Create coupon_master
    if ($coupon_master->invalidatePreviousYearCoupons()) {
        echo json_encode(
            array('message' => 'Coupon(s) invalidated successfully')
        );
    } else {
        $exp = new CustomException('Failed to invalidate coupon(s)');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}