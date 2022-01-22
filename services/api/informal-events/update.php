<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/InformalEvent.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate informal_event object
    $informal_event = new InformalEvent($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $informal_event = copyObject(
        $data,
        $informal_event,
        [
            "timeline_image_url", "image_url",
            "created_at",
            "updated_at"
        ]
    );

    // Update informal_event
    if ($informal_event->update()) {
        echo json_encode(
            array('message' => 'InformalEvent Updated')
        );
    } else {
        $exp = new CustomException('InformalEvent not updated.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
