<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../../common/APIUtil.php';
include_once __DIR__ . '/../../../models/User/AppUser.php';
include_once __DIR__ . '/../../../models/User/Cart/Cart.php';
include_once __DIR__ . '/../../../models/User/Order/Booking.php';
include_once __DIR__ . "/../../../services/mail/UserAutoProvision/UserAutoProvision.php";

use Laminas\Config\Factory;
// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    try {
        // Get raw posted data
        $data = json_decode(file_get_contents("php://input"));
        // create bookings
        $bookings_data = $data->bookings;
        foreach ($bookings_data as $booking_data) {
            $booking_model = new Booking($db);
            $booking_model->product_id = $booking_data->product_id;
            $booking_model->quantity = $booking_data->quantity;
            $booking_model->price = $booking_data->price;
            $booking_model->order_id = null;
            $booking_model->product_name = $booking_data->product_name;
            $booking_model->product_type = $booking_data->product_type;
            $booking_model->product_image = $booking_data->product_image;
            $booking_model->status = $STATUS_SUCCESS;
            $booking_model->ticket_no = uniqid("TRBS");
            $booking_model->venue = $booking_data->venue;
            $booking_model->time = $booking_data->time;

            $isNewUser = false;
            $newUserPassword = uniqid();
            $app_user_model = new AppUser($db);
            $appUserResult = $app_user_model->read(
                (object)array("filters" => array((object)array("field_name" => 'email_id', "value" => $booking_data->email_id, "op" => "=")), "filter_op" => "AND", "sort" => array())
            );
            if ($appUserResult->rowCount() > 0) {
                // user already present in db
                $app_user = $appUserResult->fetch(PDO::FETCH_ASSOC);
                $booking_model->user_id = $app_user['app_user_id'];
            } else {
                // no user found in db
                $isNewUser = true;
                $app_user_model->first_name = null;
                $app_user_model->last_name = null;
                $app_user_model->email_id = $booking_data->email_id;
                $app_user_model->mobile_no = null;
                $app_user_model->password = password_hash($newUserPassword, PASSWORD_DEFAULT);
                $app_user_model->college_id = null;
                $app_user_model->year = null;
                $app_user_model->address = null;
                $app_user_model->email_validated = 1;
                $app_user_model->state_id = null;
                $app_user_model->profile_image_id = null;
                $app_user_model->role = "user";
                $app_user_model->login_id = $booking_data->email_id;
                $app_user_model->is_active = 1;
                $cart = new Cart($db);
                $cart->total = 0;
                $cart_id = $cart->create();
                if ($cart_id) {
                    $app_user_model->cart_id = $cart_id;
                    $new_app_user_id = $app_user_model->create();
                    if ($new_app_user_id) {
                        $booking_model->user_id = $new_app_user_id;
                    } else {
                        $exp = new CustomException("User was not created.");
                        $exp->sendBadRequest();
                        exit(1);
                    }
                } else {
                    $exp = new CustomException("User Cart was not created.");
                    $exp->sendBadRequest();
                    exit(1);
                }
            }
            $booking_id = $booking_model->create();
            if (!$booking_id) {
                $exp = new CustomException("Booking was not added.");
                $exp->sendBadRequest();
                exit(1);
            }

            if ($isNewUser) {
                $config = Factory::fromFile('../../../auth/config.php', true);
                // send the confirmation email
                $mail = new UserAutoProvision();
                $mail->setTo($app_user_model->email_id, 'New TRBS User');
                $mail->setFrom($config->get('tenantEmailId'), $config->get('tenantEmailName'));
                $mail->setUserPassword($newUserPassword);
                $mail->sendMail();
            }
        }
        echo json_encode(array('message' => "Bookings has been added successfully."));
    } catch (Exception $exp) {
        message_logger($exp);
        $exp = new CustomException("Something went wrong.");
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
