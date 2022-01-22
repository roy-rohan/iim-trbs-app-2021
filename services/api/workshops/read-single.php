<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Workshop.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog workshop object
    $workshop = new Workshop($db);

    // Get ID
    $workshop->workshop_id = isset($_GET['id']) ? $_GET['id'] : die();

    // Get workshop
    $workshop->read_single();

    if (!$workshop->title) {
        echo json_encode(array('message' => 'No Workshop Found'));
    } else {
        $workshop_arr = generateResponseArray($workshop, []);
        echo json_encode($workshop_arr);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
