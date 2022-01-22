<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Member.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $member_model = new Member($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $member_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // Members array
        $member_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            $member = copyArray($row, []);

            // Push to "data"
            array_push($member_arr, $member);
        }

        // Turn to JSON & output
        echo json_encode($member_arr);
    } else {
        // No Members
        echo json_encode(
            array()
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
