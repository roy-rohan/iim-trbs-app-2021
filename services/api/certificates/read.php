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

    $certificate = new Certificate($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $certificate->read($data);

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
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
