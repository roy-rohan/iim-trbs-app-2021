<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Strategizer.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog strategizer object
    $strategizer = new Strategizer($db);

    // Get ID
    $strategizer->leaderboard_id = isset($_GET['id']) ? $_GET['id'] : die();

    // Get strategizer
    $strategizer->read_single();
    if ($strategizer->leaderboard_id) {
        $strategizer_arr = array(
            'leaderboard_id' => $strategizer->leaderboard_id,
            'name' => $strategizer->name,
            'type' => $strategizer->type,
            'score' => $strategizer->score,
            'college' => $strategizer->college,
            'college_id' => $strategizer->college_id,
            'created_at' => $strategizer->created_at,
            'updated_at' => $strategizer->updated_at
        );

        echo json_encode($strategizer_arr);
    } else {
        $exp = new CustomException('No matched record found.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
