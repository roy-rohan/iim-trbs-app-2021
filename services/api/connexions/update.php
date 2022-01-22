<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Connextion.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate connexion object
    $connexion = new Connextion($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $connexion = copyObject(
        $data,
        $connexion,
        [
            "image_url",
            "created_at",
            "updated_at"
        ]
    );

    // Update connexion
    if ($connexion->update()) {
        echo json_encode(
            array('message' => 'Event Updated')
        );
    } else {
        $exp = new CustomException('Event not updated.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
