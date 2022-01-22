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

    $connexion_model = new Connextion($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $connexion_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // Connextions array
        $connexion_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            $connexion = copyArray($row, []);

            // Push to "data"
            array_push($connexion_arr, $connexion);
        }

        // Turn to JSON & output
        echo json_encode($connexion_arr);
    } else {
        // No Connextions
        echo json_encode(
            array()
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
