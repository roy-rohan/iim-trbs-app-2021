<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Connextion.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog connexion object
    $connexion = new Connextion($db);

    // Get ID
    $connexion->connexion_id = isset($_GET['id']) ? $_GET['id'] : die();

    // Get connexion
    $connexion->read_single();

    if (!$connexion->slug) {
        echo json_encode(array('message' => 'No Connextion Found'));
    } else {
        $connexion_arr = generateResponseArray($connexion, []);
        echo json_encode($connexion_arr);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
