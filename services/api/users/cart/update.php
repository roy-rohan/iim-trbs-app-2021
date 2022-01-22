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

    try {
        $cart_model->cart_id = $data->cart_id;
        $cart_model->read_single();
        if ($cart_model->total != null) {
            $cart_model->total = $data->total;
            $cart_model->discount = $data->discount;
            $cart_model->sub_total = $data->sub_total;
            $cart_model->coupon_id = $data->coupon_id;
            $cart_model->update();
            $cart_item_model = new CartItem($db);
            $cart_items = $data->cart_items;
            foreach ($cart_items as $cart_item) {
                switch ($cart_item->state) {
                    case "1":
                        $cart_item_model->cart_id = $cart_item->cart_id;
                        $cart_item_model->quantity = $cart_item->quantity;
                        $cart_item_model->price = $cart_item->price;
                        $cart_item_model->product_id = $cart_item->product_id;
                        $cart_item_model->product_slug = $cart_item->product_slug;
                        $cart_item_model->product_name = $cart_item->product_name;
                        $cart_item_model->product_type = $cart_item->product_type;
                        $cart_item_model->product_image = $cart_item->product_image;
                        $cart_item_model->venue = $cart_item->venue;
                        $cart_item_model->event_date = $cart_item->event_date;
                        $cart_item_model->create();
                        break;
                    case "0":
                        $cart_item_model->cart_item_id = $cart_item->cart_item_id;
                        $cart_item_model->cart_id = $cart_item->cart_id;
                        $cart_item_model->quantity = $cart_item->quantity;
                        $cart_item_model->price = $cart_item->price;
                        $cart_item_model->product_id = $cart_item->product_id;
                        $cart_item_model->product_name = $cart_item->product_name;
                        $cart_item_model->product_type = $cart_item->product_type;
                        $cart_item_model->product_image = $cart_item->product_image;
                        $cart_item_model->venue = $cart_item->venue;
                        $cart_item_model->event_date = $cart_item->event_date;
                        $cart_item_model->update();
                        break;
                    case "-1":
                        $cart_item_model->cart_item_id = $cart_item->cart_item_id;
                        $cart_item_model->delete();
                        break;
                }
            }
            echo json_encode(
                array('message' => 'Cart updated sucessfully.')
            );
        } else {
            // No Cart
            $exp = new CustomException("No cart found.");
            $exp->sendBadRequest();
        }
    } catch (Exception $exp) {
        // No Cart
        $exp = new CustomException("Something went wrong.");
        $exp->sendServerException();
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
