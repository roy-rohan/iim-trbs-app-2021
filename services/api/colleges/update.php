<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/College.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate interestedUser object
    $college = new College($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Set ID to update
    $college->college_id = $data->college_id;

    $college->name = $data->name;
    $college->is_active = $data->is_active;
	
    // Update interestedUser
    if ($college->update()) {
        echo json_encode(
            array('message' => 'College record Updated')
        );
    } else {
        echo json_encode(
            array('message' => 'College record Not Updated')
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
