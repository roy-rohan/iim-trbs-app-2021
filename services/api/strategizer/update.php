<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Strategizer.php';
include_once __DIR__ . '/../../models/College.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate interestedUser object
    $strategizer = new Strategizer($db);
    $college = new College($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Set ID to update
    $strategizer->leaderboard_id = $data->leaderboard_id;

    $strategizer->type = $data->type;
    $strategizer->name = $data->name;
    $strategizer->score = $data->score;
    // Check if college exists
    $college->college_id = $data->college_id;

    if (!$college->read_single()) {
        $college->name = $data->college_name;
        $new_college_id = $college->create();
        if (!$new_college_id) {
            echo json_encode(
                array('message' => 'College record was not created.')
            );
            return;
        } else {
            $strategizer->college_id = $new_college_id;
        }
    } else {
        $strategizer->college_id = $data->college_id;
    }

    // Update interestedUser
    if ($strategizer->update()) {
        echo json_encode(
            array('message' => 'Leaderboard record Updated')
        );
    } else {
        echo json_encode(
            array('message' => 'Leaderboard record Not Updated')
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
