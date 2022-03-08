<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Certificate.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate certificate object
    $certificate = new Certificate($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $certificate = copyObject(
        $data,
        $certificate,
        [
            "image_url",
            "certificate_id", "created_at",
            "updated_at"
        ]
    );

    // Create certificate
    $new_certificate_id = $certificate->create();
    if ($new_certificate_id) {
        echo json_encode(
            array('message' => 'Certificate Created', 'id' => $new_certificate_id)
        );
    } else {
        $exp = new CustomException('Certificate not created.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
