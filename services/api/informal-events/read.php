<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/InformalEvent.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $informal_event_model = new InformalEvent($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $informal_event_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // InformalEvents array
        $informal_event_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            $informal_event = copyArray($row, []);

            // Push to "data"
            array_push($informal_event_arr, $informal_event);
        }

        // Turn to JSON & output
        echo json_encode($informal_event_arr);
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
