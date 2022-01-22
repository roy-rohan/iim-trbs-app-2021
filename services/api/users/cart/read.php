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

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $cart_model = new Cart($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $cart_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {

        if ($num < 2) {
            $cartResult = $result->fetch(PDO::FETCH_ASSOC);
            $cart_item_model = new CartItem($db);
            $cartItemQuery = (object)array("filters" => array((object)array("field_name" => 'cart_id', "value" => $cartResult['cart_id'], "op" => "=")), "filter_op" => "AND", "sort" => array());

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
                "coupon_id" => $cartResult['coupon_id'],
                "coupon_code" => $cartResult['coupon_code'],
                "status" => $cartResult['status'],
                "applied_on" => $cartResult['applied_on'],
                "discount" => $cartResult['discount'],
                "created_at" => $cartResult['created_at'],
                "updated_at" => $cartResult['updated_at'],
            );

            $cart = array(
                "cart_id" => $cartResult['cart_id'],
                "total" => $cartResult['total'],
                "sub_total" => $cartResult['sub_total'],
                "discount" => $cartResult['discount'],
                "coupon" => $coupon,
                "cart_items" => $cart_items
            );

            // Turn to JSON & output
            echo json_encode($cart);
        } else {
            $exp = new CustomException("Multiple records found.");
            $exp->sendBadRequest();
        }
    } else {
        // No Events
        $exp = new CustomException("No cart found.");
        $exp->sendBadRequest();
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
