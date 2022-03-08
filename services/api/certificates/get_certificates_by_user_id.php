<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/UserCertificate.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog userCertificate object
    $userCertificate = new UserCertificate($db);

    // Get ID
    $user_id = isset($_GET['id']) ? $_GET['id'] : die();

    // Get userCertificate
    $result = $userCertificate->getCertificatesByUserId($user_id);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // Certificates array
        $certificate_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            $certificate = copyArray($row, []);

            // Push to "data"
            array_push($certificate_arr, $certificate);
        }

        // Turn to JSON & output
        echo json_encode($certificate_arr);
    } else {
        // No Certificates
        echo json_encode(
            array()
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
