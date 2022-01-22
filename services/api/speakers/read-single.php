<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Speaker.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog speaker object
    $speaker = new Speaker($db);

    // Get ID
    $speaker->speaker_id = isset($_GET['id']) ? $_GET['id'] : die();

    // Get speaker
    $speaker->read_single();

    if (!$speaker->slug) {
        echo json_encode(array('message' => 'No Speaker Found'));
    } else {
        $speaker_arr = generateResponseArray($speaker, []);
        echo json_encode($speaker_arr);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
