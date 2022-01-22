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

    // Instantiate speaker object
    $speaker = new Speaker($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $speaker = copyObject(
        $data,
        $speaker,
        [
            "image_url",
            "speaker_id", "created_at",
            "updated_at"
        ]
    );

    // Create speaker
    $new_speaker_id = $speaker->create();
    if ($new_speaker_id) {
        echo json_encode(
            array('message' => 'Speaker Created', 'id' => $new_speaker_id)
        );
    } else {
        $exp = new CustomException('Speaker not created.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
