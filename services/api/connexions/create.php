<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Connextion.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate connexion object
    $connexion = new Connextion($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $connexion = copyObject(
        $data,
        $connexion,
        [
            "image_url",
            "connexion_id", "created_at",
            "updated_at"
        ]
    );

    // Create connexion
    $new_connexion_id = $connexion->create();
    if ($new_connexion_id) {
        echo json_encode(
            array('message' => 'Connextion Created', 'id' => $new_connexion_id)
        );
    } else {
        $exp = new CustomException('Connextion not created.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
