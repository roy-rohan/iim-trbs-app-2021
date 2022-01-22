<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Sponser.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog sponser object
    $sponser = new Sponser($db);

    // Get ID
    $sponser->sponser_id = isset($_GET['id']) ? $_GET['id'] : die();

    // Get sponser
    $sponser->read_single();

    if (!$sponser->created_at) {
        echo json_encode(array('message' => 'No Sponser Found'));
    } else {
        $sponser_arr = generateResponseArray($sponser, []);
        echo json_encode($sponser_arr);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
