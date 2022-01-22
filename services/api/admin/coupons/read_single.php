<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../../common/APIUtil.php';
include_once __DIR__ . '/../../../models/Admin/CouponMaster.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog couponMaster object
    $couponMaster = new CouponMaster($db);

    // Get ID
    $couponMaster->coupon_id = isset($_GET['id']) ? $_GET['id'] : die();

    // Get couponMaster
    $couponMaster->read_single();

    if (!$couponMaster->coupon_code) {
        $exp = new CustomException('No Coupon Found');
        $exp->sendBadRequest();
        exit(1);
    } else {
        $coupon_master_arr = array(
            'coupon_id' => $couponMaster->coupon_id,
            'coupon_code' => $couponMaster->coupon_code,
            'status' => $couponMaster->status,
            'discount' => $couponMaster->discount,
            'applied_on' => $couponMaster->applied_on,
            'created_at' => $couponMaster->created_at,
            'updated_at' => $couponMaster->updated_at
        );

        echo json_encode($coupon_master_arr);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
