<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Pack.php';
include_once __DIR__ . '/../../models/PackItem.php';


if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate informal_event object
    $pack = new Pack($db);
    $packItem = new PackItem($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Set ID to delete
    $pack->pack_id = $data->id;
    $pack->read_single();
    if ($pack->name) {
        $packItemsResult = $packItem->read((object)array("filters" => array((object)array("field_name" => 'pack_id', "value" => $pack->pack_id, "op" => "=")), "filter_op" => "AND", "sort" => array()));
        while ($packItemResult = $packItemsResult->fetch(PDO::FETCH_ASSOC)) {
            $packItem->pack_item_id = $packItemResult["pack_item_id"];
            if (!$packItem->delete()) {
                message_logger("Unable to delete pack item with id: " . $packItem->pack_item_id);
            }
        }
        if ($pack->delete()) {
            echo json_encode(
                array('message' => 'pack Deleted')
            );
        } else {
            $exp = new CustomException("pack not found with id: " . $data->id);
            $exp->sendBadRequest();
        }
    } else {
        $exp = new CustomException("pack not found with id: " . $data->id);
        $exp->sendBadRequest();
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
