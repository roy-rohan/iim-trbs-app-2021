<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/AppUser.php';
include_once __DIR__ . '/../../models/Image.php';


if ($_SERVER['REQUEST_METHOD'] == "DELETE") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate app_user object
    $app_user = new AppUser($db);
    $image = new Image($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Set ID to delete
    $app_user->app_user_id = $data->app_user_id;
    $app_user->read_single();
    if ($app_user->email_id) {
        $image->image_id = $app_user->profile_image_id;
        if ($app_user->delete()) {
            if (!$image->delete()) {
                message_logger("Image for app_user: " . $data->app_user_id . " with app_user_id: " . $image->image_id . "was not deleted");
            }
            echo json_encode(
                array('message' => 'User Deleted')
            );
        } else {
            $exp = new CustomException("User not found.");
            $exp->sendBadRequest();
        }
    } else {
        $exp = new CustomException("User not found.");
        $exp->sendBadRequest();
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
