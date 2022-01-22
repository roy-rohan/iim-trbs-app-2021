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

    // Instantiate informal_event object
    $informal_event = new InformalEvent($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $informal_event = copyObject(
        $data,
        $informal_event,
        [
            "timeline_image_url", "image_url",
            "informal_event_id", "created_at",
            "updated_at"
        ]
    );

    // Create informal_event
    $new_informal_event_id = $informal_event->create();
    if ($new_informal_event_id) {
        echo json_encode(
            array('message' => 'InformalEvent Created', 'id' => $new_informal_event_id)
        );
    } else {
        $exp = new CustomException('InformalEvent not created.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
