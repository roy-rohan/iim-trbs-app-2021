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

    $pack_model = new Pack($db);
    $packItem = new PackItem($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $pack_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // InformalEvents array
        $pack_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            $pack = copyArray($row, []);
            $pack_item_arr = array();
            $packItemsResult = $packItem->read((object)array("filters" => array((object)array("field_name" => 'pack_id', "value" => $pack["pack_id"], "op" => "=")), "filter_op" => "AND", "sort" => array()));
            while ($packItemRow = $packItemsResult->fetch(PDO::FETCH_ASSOC)) {
                $packItemResponse = copyArray($packItemRow, []);
                array_push($pack_item_arr, $packItemResponse);
            }
            $pack["pack_items"] = $pack_item_arr;
            // Push to "data"
            array_push($pack_arr, $pack);
        }

        // Turn to JSON & output
        echo json_encode($pack_arr);
    } else {
        // No InformalEvents
        echo json_encode(
            array()
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
