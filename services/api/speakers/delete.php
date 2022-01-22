<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Speaker.php';
include_once __DIR__ . '/../../models/Image.php';


if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate speaker object
    $speaker = new Speaker($db);
    $image = new Image($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Set ID to delete
    $speaker->speaker_id = $data->id;
    $speaker->read_single();
    if ($speaker->slug) {
        $image->entity_id = $speaker->speaker_id;
        $image->entity_type = "speaker";
        $image->deleteByEntity();
        if ($speaker->delete()) {
            echo json_encode(
                array('message' => 'Speaker Deleted')
            );
        } else {
            $exp = new CustomException("Speaker not found with id: " . $data->id);
            $exp->sendBadRequest();
        }
    } else {
        $exp = new CustomException("Speaker not found with id: " . $data->id);
        $exp->sendBadRequest();
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
