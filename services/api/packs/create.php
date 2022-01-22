<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Pack.php';
include_once __DIR__ . '/../../models/PackItem.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate pack object
    $pack = new Pack($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $pack->name = $data->name;
    $pack->type = $data->type;
    $pack->pack_desc = $data->pack_desc;
    $pack->is_active = $data->is_active;

    // Create pack
    $new_pack_id = $pack->create();
    if ($new_pack_id) {
        $packItem = new PackItem($db);
        $count = (int)count($data->pack_items);
        for ($index = 0; $index < $count; $index++) {
            $packItem->pack_id = $new_pack_id;
            $packItem->product_id = $data->pack_items[$index]->product_id;
            $packItem->product_image = $data->pack_items[$index]->product_image;
            $packItem->product_name = $data->pack_items[$index]->product_name;
            $packItem->product_type = $data->pack_items[$index]->product_type;
            $packItem->price = $data->pack_items[$index]->price;
            $packItem->slug = $data->pack_items[$index]->slug;
            $new_pack_item_id = $packItem->create();
            if (!$new_pack_item_id) {
                $exp = new CustomException('Failed to Create Pack Item: ' . $count);
                $exp->sendBadRequest();
                exit(1);
            }
        }
        echo json_encode(
            array('message' => 'Pack Created', 'id' => $new_pack_id)
        );
    } else {
        $exp = new CustomException('Pack not created.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
