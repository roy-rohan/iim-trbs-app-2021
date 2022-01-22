<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Image.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $image_model = new Image($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $image_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // Events array
        $event_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            extract($row);

            $event = array(
                'image_id' => $image_id,
                'entity_type' => $entity_type,
                'entity_id' => $entity_id,
                'path' => $path,
                'type' => $type,
                'created_at' => $created_at,
                'updated_at' => $updated_at,
            );

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
