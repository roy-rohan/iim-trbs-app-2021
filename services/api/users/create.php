<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/User/AppUser.php';
include_once __DIR__ . '/../../models/User/Cart/Cart.php';
include_once __DIR__ . '/../../auth/token_utils.php';
include_once __DIR__ . "/../../services/mail/ProfileActivation/ProfileActivation.php";

use Laminas\Config\Factory;

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate app_user object
    $app_user = new AppUser($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $app_user->first_name = $data->first_name;
    $app_user->last_name = $data->last_name;
    $app_user->email_id = $data->email_id;
    $app_user->mobile_no = $data->mobile_no;
    $app_user->password = password_hash($data->password, PASSWORD_DEFAULT);
    $app_user->college_id = $data->college_id;
    $app_user->year = $data->year;
    $app_user->address = $data->address;
    $app_user->email_validated = 0;
    $app_user->state_id = $data->state_id;
    $app_user->profile_image_id = $data->profile_image_id;
    $app_user->role = $data->role;
    $app_user->login_id = $data->login_id;
    $app_user->is_active = $data->is_active;
    $app_user->external_type = "FORM_LOGIN";

    try {
        if (!$app_user->checkAppUser()) {
            $cart = new Cart($db);
            $cart->total = 0;
            $cart_id = $cart->create();
            if ($cart_id) {
                // create the user
                $app_user->cart_id = $cart_id;
                $new_app_user_id = $app_user->create();
                if ($new_app_user_id) {
                    $config = Factory::fromFile('../../auth/config.php', true);
                    // send the confirmation email
                    $profileActivationLink = generateProfileActivationLink($new_app_user_id);
                    $mail = new ProfileActivation();
                    $mail->setTo($app_user->email_id, $app_user->first_name);
                    $mail->setFrom($config->get('tenantEmailId'), $config->get('tenantEmailName'));
                    $mail->setActivationLink($profileActivationLink);
					$mail->setName($data->first_name);
                    $mail->sendMail();
                    echo json_encode(
                        array('message' => 'User has been Created.', 'id' => $new_app_user_id)
                    );
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
        } else {
            $exp = new CustomException('Email Id is already associated with another account.');
            $exp->sendBadRequest();
            exit(1);
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

function generateProfileActivationLink($user_id)
{
    $data = ["user_id" => $user_id];
    $token = encodeToken($data);
    $config = Factory::fromFile('../../auth/config.php', true);
    // TO BE REMOVED IN PROD
    // return "http://localhost/iim-app-services/api/users/activate.php?token=$token";
    return $config->get('frontendUrl') . "/user/activate?token=" . $token;
}
