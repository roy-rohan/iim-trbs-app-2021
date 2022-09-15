<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Authorization');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/User/AppUser.php';
include_once __DIR__ . '/../../auth/token_utils.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog app_user object
    $app_user = new AppUser($db);

    $data = json_decode(file_get_contents("php://input"));
    $user_data = authenicateRequest($data->token);
    $app_user->app_user_id = isset($user_data->user_id) ? $user_data->user_id : die();

    // Get app_user
    $app_user->read_single();
    if (!$app_user->email_id) {
        echo json_encode(array('message' => 'No User Found'));
    } else {
        $app_user_arr = array(
            'app_user_id' => $app_user->app_user_id,
            'first_name' => $app_user->first_name,
            'last_name' => $app_user->last_name,
            'email_id' => $app_user->email_id,
            'mobile_no' => $app_user->mobile_no,
            'college' => $app_user->college,
            'college_id' => $app_user->college_id,
            'year' => $app_user->year,
            'address' => $app_user->address,
            'email_validated' => $app_user->email_validated,
            'state' => $app_user->state,
            'state_id' => $app_user->state_id,
            'profile_image' => $app_user->profile_image,
            'profile_image_url' => $app_user->profile_image_url,
            'role' => $app_user->role,
            'cart_id' => $app_user->cart_id,
            'is_active' => $app_user->is_active,
            'login_id' => $app_user->login_id,
            'created_by' => $app_user->created_by,
            'updated_by' => $app_user->updated_by,
            'created_at' => $app_user->created_at,
            'updated_at' => $app_user->updated_at,
        );

        echo json_encode($app_user_arr);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
