<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Event.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "GET") {

    $event_model = new Event($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $event_model->getCategories();

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // Categories array
        $category_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $category = $type;

            // Push to "data"
            array_push($category_arr, $category);
        }

        // Turn to JSON & output
        echo json_encode($category_arr);
    } else {
        // No Categories
        echo json_encode(
            array()
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
