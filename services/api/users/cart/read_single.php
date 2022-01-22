<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../../common/APIUtil.php';
include_once __DIR__ . '/../../../models/User/Cart/Cart.php';
include_once __DIR__ . '/../../../models/User/Cart/CartItem.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "GET") {

    $cart_model = new Cart($db);

    // Get raw posted data
    $cart_model->cart_id = isset($_GET['id']) ? $_GET['id'] : die();

    if ($cart_model->read_single()) {
        $cart_item_model = new CartItem($db);
        $cartItemQuery = (object)array("filters" => array((object)array("field_name" => 'cart_id', "value" => $cart_model->cart_id, "op" => "=")), "filter_op" => "AND", "sort" => array());

        $cartsResult = $cart_item_model->read($cartItemQuery);
        $cart_items = array();
        while ($cartItemRow = $cartsResult->fetch(PDO::FETCH_ASSOC)) {
            extract($cartItemRow);
            $cart_item = array(
                'cart_item_id' => $cart_item_id,
                'product_id' => $product_id,
                'price' => $price,
                'quantity' => $quantity,
                'product_slug' => $product_slug,
                'product_type' => $product_type,
                'product_name' => $product_name,
                'product_image' => $product_image,
                'venue' => $venue,
                'event_date' => $event_date
            );
            array_push($cart_items, $cart_item);
        }

        $coupon = (object)array(
            "coupon_id" => $cart_model->coupon_id,
            "coupon_code" => $cart_model->coupon_code,
            "status" => $cart_model->status,
            "applied_on" => $cart_model->applied_on,
            "discount" => $cart_model->discount,
            "created_at" => $cart_model->created_at,
            "updated_at" => $cart_model->updated_at,
        );

        $cart = array(
            "cart_id" => $cart_model->cart_id,
            "total" => $cart_model->total,
            "sub_total" => $cart_model->sub_total,
            "discount" => $cart_model->discount,
            "coupon" => $coupon,
            "cart_items" => $cart_items
        );

        // Turn to JSON & output
        echo json_encode($cart);
    } else {
        // No cart
        $exp = new CustomException("No cart found.");
        $exp->sendBadRequest();
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
