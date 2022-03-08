<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../utilities/enums/CertificateCategory.php';
include_once __DIR__ . '/../../services/CertificateService/CertificateService.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    try{

        $category = $data->category;

        switch ($category) {
            case CERTICATE_CATEGORY::$ALL_REGISTERED_USERS :
                CertificateService::sendToAllRegisteredUsers($db, $data);
                break;
            case CERTICATE_CATEGORY::$ALL_PARTICIPATED_USERS :
                CertificateService::sendToAllParticipatedUsers($db, $data);
                break;
            case CERTICATE_CATEGORY::$SEND_BY_USER_EMAIL :
                CertificateService::sendToUsersByEmail($db, $data);
                break;
        }

        echo json_encode(
            array('message' => 'Certificates Sent Successfully.')
        );

    } catch (Exception $error) {
        message_logger($error->getMessage());
        $exp = new CustomException('Certificates could not be sent.');
        $exp->sendBadRequest();
        exit(1);
    }

} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
