<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/College.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate college object
    $college = new College($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $college->name = $data->name;
    $college->is_active = $data->is_active;

    // Create college
    $new_college_id = $college->create();
    if ($new_college_id) {
        echo json_encode(
            array('message' => 'College Created', 'id' => $new_college_id)
        );
    } else {
        $exp = new CustomException('College not created.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
