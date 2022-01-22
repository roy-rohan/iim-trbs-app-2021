<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Sponser.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $sponser_model = new Sponser($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $sponser_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // Sponsers array
        $sponser_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            $sponser = copyArray($row, []);

            // Push to "data"
            array_push($sponser_arr, $sponser);
        }

        // Turn to JSON & output
        echo json_encode($sponser_arr);
    } else {
        // No Sponsers
        echo json_encode(
            array()
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
