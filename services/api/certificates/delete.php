<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Certificate.php';
include_once __DIR__ . '/../../models/UserCertificate.php';
include_once __DIR__ . '/../../models/Image.php';


if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate certificate object
    $certificate = new Certificate($db);
    $userCertificate = new UserCertificate($db);
    $image = new Image($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Set ID to delete
    $certificate->certificate_id = $data->id;
    $certificate->read_single();
    if ($certificate->created_at) {
        $image->entity_id = $certificate->certificate_id;
        $image->entity_type = "certificate";
        $image->deleteByEntity();
        if ($certificate->delete()) {
            $userCertificate->deleteByCertificateId($data->id);
            echo json_encode(
                array('message' => 'Certificate Deleted')
            );
        } else {
            $exp = new CustomException("Certificate not found with id: " . $data->id);
            $exp->sendBadRequest();
        }
    } else {
        $exp = new CustomException("Certificate not found with id: " . $data->id);
        $exp->sendBadRequest();
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
