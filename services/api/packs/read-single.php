<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Pack.php';
include_once __DIR__ . '/../../models/PackItem.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog pack object
    $pack_model = new Pack($db);
    $packItem = new PackItem($db);

    // Get ID
    $pack_model->pack_id = isset($_GET['id']) ? $_GET['id'] : die();

    // Get pack
    $pack_model->read_single();

    if ($pack_model->name) {
        $pack_arr = generateResponseArray($pack_model, []);
        $pack_item_arr = array();
        $packItemsResult = $packItem->read((object)array("filters" => array((object)array("field_name" => 'pack_id', "value" => $pack_model->pack_id, "op" => "=")), "filter_op" => "AND", "sort" => array()));
        while ($packItemRow = $packItemsResult->fetch(PDO::FETCH_ASSOC)) {
            $packItemResponse = copyArray($packItemRow, []);
            array_push($pack_item_arr, $packItemResponse);
        }
        $pack_arr["pack_items"] = $pack_item_arr;
        // Push to "data"
        echo json_encode($pack_arr);
    } else {
        echo json_encode(array('message' => 'No pack Found'));
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
