<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Event.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog event object
    $event = new Event($db);

    // Get ID
    $event->event_id = isset($_GET['id']) ? $_GET['id'] : die();

    // Get event
    $event->read_single();

    if (!$event->title) {
        echo json_encode(array('message' => 'No Event Found'));
    } else {
        $event_arr = generateResponseArray($event, []);
        echo json_encode($event_arr);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
