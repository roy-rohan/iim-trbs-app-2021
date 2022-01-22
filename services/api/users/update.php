<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/AppUser.php';
include_once __DIR__ . '/../../auth/token_utils.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate app_user object
    $app_user = new AppUser($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    authenicateRequest($data->token);

    $app_user->app_user_id = $data->app_user_id;
    $app_user->first_name = $data->first_name;
    $app_user->last_name = $data->last_name;
    $app_user->email_id = $data->email_id;
    $app_user->mobile_no = $data->mobile_no;
    $app_user->college_id = $data->college_id;
    $app_user->year = $data->year;
    $app_user->address = $data->address;
    $app_user->state_id = $data->state_id;
    $app_user->profile_image_id = $data->profile_image_id;
    $app_user->role = $data->role;
    $app_user->login_id = $data->login_id;
    $app_user->updated_by = $data->updated_by;
    $app_user->is_active = $data->is_active;

    if ($app_user->update()) {
        echo json_encode(
            array('message' => 'User has been updated.')
        );
    } else {
        $exp = new CustomException('User was not updated.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
