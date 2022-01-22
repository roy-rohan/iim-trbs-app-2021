<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');


include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/User/AppUser.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $app_user_model = new AppUser($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $app_user_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // app_users array
        $app_user_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            $app_user = copyArray($row, []);

            // Push to "data"
            array_push($app_user_arr, $app_user);
        }

        // Turn to JSON & output
        echo json_encode($app_user_arr);
    } else {
        // No app_users
        echo json_encode(
            array()
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
