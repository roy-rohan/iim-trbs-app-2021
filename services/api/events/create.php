<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Event.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate event object
    $event = new Event($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $event = copyObject(
        $data,
        $event,
        [
            "timeline_image_url", "image_url",
            "image_path", "event_id", "created_at",
            "updated_at"
        ]
    );

    // Create event
    $new_event_id = $event->create();
    if ($new_event_id) {
        echo json_encode(
            array('message' => 'Event Created', 'id' => $new_event_id)
        );
    } else {
        $exp = new CustomException('Event not created.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
