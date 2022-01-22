<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Speaker.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $speaker_model = new Speaker($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $speaker_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // Speakers array
        $speaker_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            $speaker = copyArray($row, []);

            // Push to "data"
            array_push($speaker_arr, $speaker);
        }

        // Turn to JSON & output
        echo json_encode($speaker_arr);
    } else {
        // No Speakers
        echo json_encode(
            array()
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
