<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/InformalEvent.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog informal_event object
    $informal_event = new InformalEvent($db);

    // Get ID
    $informal_event->informal_event_id = isset($_GET['id']) ? $_GET['id'] : die();

    // Get informal_event
    $informal_event->read_single();

    if (!$informal_event->title) {
        echo json_encode(array('message' => 'No InformalEvent Found'));
    } else {
        $informal_event_arr = generateResponseArray($informal_event, []);
        echo json_encode($informal_event_arr);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
