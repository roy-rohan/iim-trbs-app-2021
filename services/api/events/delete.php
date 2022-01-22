<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Event.php';
include_once __DIR__ . '/../../models/Image.php';


if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate event object
    $event = new Event($db);
    $image = new Image($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Set ID to delete
    $event->event_id = $data->id;
    $event->read_single();
    if ($event->title) {
        $image->entity_id = $event->event_id;
        $image->entity_type = "event";
        $image->deleteByEntity();
        if ($event->delete()) {
            // if ($image->deleteByEntity()) {
            //     echo "deleted";
            //     message_logger("Image for event: " . $data->id . " with id: " . $image->image_id . "was not not deleted");
            // } else {
            //     message_logger("Image for event: " . $data->id . " with deleted");
            //     echo "not deleted";
            // }
            echo json_encode(
                array('message' => 'Event Deleted')
            );
        } else {
            $exp = new CustomException("::Event not found with id: " . $data->id);
            $exp->sendBadRequest();
        }
    } else {
        $exp = new CustomException("Event not found with id: " . $data->id);
        $exp->sendBadRequest();
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
