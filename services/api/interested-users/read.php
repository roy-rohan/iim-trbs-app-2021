<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/InterestedUser.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $interested_user_model = new InterestedUser($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $interested_user_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // Users array
        $interested_user_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $interested_user = array(
                'interested_user_id' => $interested_user_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email_id' => $email_id,
                'mobile_no' => $mobile_no,
                'college' => $college,
                'state' => $state,
                'event_name' => $event_name,
                'created_at' => $created_at,
                'updated_at' => $updated_at
            );

            // Push to "data"
            array_push($interested_user_arr, $interested_user);
        }

        message_logger("User data fetched.");
        // Turn to JSON & output
        echo json_encode($interested_user_arr);
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
