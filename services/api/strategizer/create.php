<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Strategizer.php';
include_once __DIR__ . '/../../models/College.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate strategizer object
    $strategizer = new Strategizer($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $strategizer->type = $data->type;
    $strategizer->name = $data->name;
    $strategizer->score = $data->score;
    $strategizer->college_id = $data->college_id;
    $college = new College($db);
    $college->college_id = $data->college_id;
    if (!$college->read_single()) {
        $college->name = $data->college_name;
        $college->is_active = 1;
        $new_college_id = $college->create();
        if ($new_college_id) {
            $strategizer->college_id = $new_college_id;
        }
    }
    // Create strategizer
    $new_strategizer_id = $strategizer->create();
    if ($new_strategizer_id) {
        echo json_encode(
            array('message' => 'Leaderboard Created', 'id' => $new_strategizer_id)
        );
    } else {
        $exp = new CustomException('Leaderboard record not created.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
