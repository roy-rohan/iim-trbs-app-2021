<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Pack.php';
include_once __DIR__ . '/../../models/PackItem.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate pack object
    $pack = new Pack($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $pack->pack_id = $data->id;
    $pack->name = $data->name;
    $pack->type = $data->type;
    $pack->pack_desc = $data->pack_desc;
    $pack->is_active = $data->is_active;

    // Update pack
    if ($pack->update()) {
        $packItem = new PackItem($db);
        $count = (int)count($data->pack_items);
        for ($index = 0; $index < $count; $index++) {
            $packItemRequest = $data->pack_items[$index];
            switch ($packItemRequest->state) {
                case "1":
                    $packItem->pack_id = $packItemRequest->pack_id;
                    $packItem->product_id = $packItemRequest->product_id;
                    $packItem->product_image = $packItemRequest->product_image;
                    $packItem->product_name = $packItemRequest->product_name;
                    $packItem->product_type = $packItemRequest->product_type;
                    $packItem->price = $packItemRequest->price;
                    $packItem->slug = $packItemRequest->slug;
                    $new_pack_item_id = $packItem->create();
                    break;
                case "0":
                    $packItem->pack_item_id = $packItemRequest->pack_item_id;
                    $packItem->pack_id = $packItemRequest->pack_id;
                    $packItem->product_id = $packItemRequest->product_id;
                    $packItem->product_image = $packItemRequest->product_image;
                    $packItem->product_name = $packItemRequest->product_name;
                    $packItem->product_type = $packItemRequest->product_type;
                    $packItem->price = $packItemRequest->price;
                    $packItem->slug = $packItemRequest->slug;
                    $packItem->update();
                    break;
                case "-1":
                    $packItem->pack_item_id = $packItemRequest->pack_item_id;
                    $packItem->delete();
                    break;
            }
        }
        echo json_encode(
            array('message' => 'pack Updated')
        );
    } else {
        $exp = new CustomException('pack not updated.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
