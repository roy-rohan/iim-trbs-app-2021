<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/State.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $state_model = new State($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $state_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // States array
        $state_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            extract($row);

            $event = array(
                'state_id' => $state_id,
                'name' => $name,
                'created_at' => $created_at,
                'updated_at' => $updated_at,
            );

            // Push to "data"
            array_push($state_arr, $event);
        }

        // Turn to JSON & output
        echo json_encode($state_arr);
    } else {
        // No States
        echo json_encode(
            array()
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
