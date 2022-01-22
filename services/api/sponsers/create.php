<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Sponser.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate sponser object
    $sponser = new Sponser($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $sponser = copyObject(
        $data,
        $sponser,
        [
            "timeline_image_url", "image_url",
            "sponser_id", "created_at",
            "updated_at"
        ]
    );

    // Create sponser
    $new_sponser_id = $sponser->create();
    if ($new_sponser_id) {
        echo json_encode(
            array('message' => 'Sponser Created', 'id' => $new_sponser_id)
        );
    } else {
        $exp = new CustomException('Sponser not created.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
