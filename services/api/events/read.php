<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');


include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Event.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $event_model = new Event($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $event_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // Events array
        $event_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            $event = copyArray($row, []);

            // Push to "data"
            array_push($event_arr, $event);
        }

        // Turn to JSON & output
        echo json_encode($event_arr);
    } else {
        // No Events
        echo json_encode(
            array()
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
