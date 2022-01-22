<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Strategizer.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $strategizer_model = new Strategizer($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $strategizer_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // Users array
        $strategizer_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $strategizer = array(
                'leaderboard_id' => $leaderboard_id,
                'name' => $name,
                'type' => $type,
                'score' => $score,
                'college' => $college,
                'created_at' => $created_at,
                'updated_at' => $updated_at
            );

            // Push to "data"
            array_push($strategizer_arr, $strategizer);
        }

        message_logger("Leaderboard data fetched.");
        // Turn to JSON & output
        echo json_encode($strategizer_arr);
    } else {
        // No Users
        echo json_encode(
            array()
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
