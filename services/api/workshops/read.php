<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Workshop.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $workshop_model = new Workshop($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $workshop_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // Workshops array
        $workshop_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            $workshop = copyArray($row, []);

            // Push to "data"
            array_push($workshop_arr, $workshop);
        }

        // Turn to JSON & output
        echo json_encode($workshop_arr);
    } else {
        // No Workshops
        echo json_encode(
            array()
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
