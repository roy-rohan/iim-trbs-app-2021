<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/User/AppUser.php';
include_once __DIR__ . '/../../models/User/Cart/Cart.php';
include_once __DIR__ . '/../../auth/token_utils.php';

use Laminas\Config\Factory;

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate app_user object
    $app_user = new AppUser($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $app_user->login_id = $data->login_id;
    $app_user->profile_image_url = $data->profile_image_url;
    $app_user->email_id = $data->email_id;
    $app_user->external_id = hash("sha256", $data->aud . $data->email_id);
    $app_user->external_type = "GOOGLE";

    try {
        if (!$app_user->checkGoogleUser()) {
            if($app_user->checkAppUser()) {
                $app_user->email_validated = 1;
                if($app_user->updateExternalUserAttributes()) {
                    $app_user->read_external_user();
                    $data = [
                        "user_id" => $app_user->app_user_id,
                        "first_name" => $app_user->first_name
                    ];
                    $token = encodeToken($data);
                    $response = ['token' => $token, 'message' => 'login successfull'];
                    echo json_encode($response);
                } else {
                    $exp = new CustomException('Something went wrong.');
                    $exp->sendBadRequest();
                    exit(1);
                }
            } else {
                $app_user->first_name = $data->first_name;
                $app_user->last_name = $data->last_name;
                $app_user->email_validated = 1;
                $app_user->state_id = $data->state_id;
                $app_user->role = 'user';
                $app_user->password = $app_user->external_id;
                $app_user->is_active = $data->is_active;

                $cart = new Cart($db);
                $cart->total = 0;
                $cart_id = $cart->create();
                if ($cart_id) {
                    // create the user
                    $app_user->cart_id = $cart_id;
                    $new_app_user_id = $app_user->create();
                    if ($new_app_user_id) {
                        $app_user->read_external_user();

                        $data = [
                            "user_id" => $app_user->app_user_id,
                            "first_name" => $app_user->first_name
                        ];
                        $token = encodeToken($data);
                        $response = ['token' => $token, 'message' => 'login successfull'];
                        echo json_encode($response);
                    } else {
                        $exp = new CustomException('Something went wrong. User was not created.');
                        $exp->sendBadRequest();
                        exit(1);
                    }
                } else {
                    $exp = new CustomException('User cart could not be created.');
                    $exp->sendBadRequest();
                    exit(1);
                }
            }
        } else {
            $app_user->read_external_user();

            $data = [
                "user_id" => $app_user->app_user_id,
                "first_name" => $app_user->first_name
            ];
            $token = encodeToken($data);
            $response = ['token' => $token, 'message' => 'login successfull'];
            echo json_encode($response);
        }
    } catch (Exception $e) {
        $exp = new CustomException('Something went wrong.');
        $exp->sendServerException();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}