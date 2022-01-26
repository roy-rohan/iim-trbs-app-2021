<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Image.php';


if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate image object
    $image = new Image($db);
    // Get raw posted data

    $response = array();
    $upload_dir = __DIR__ . '/../../uploads/';
    $server_url = 'http://localhost/iim-app-services';

    if ($_FILES['image_upload']) {
        $image_upload = str_replace(' ', '_', basename($_FILES["image_upload"]["name"]));
        $image_tmp_name = $_FILES["image_upload"]["tmp_name"];
        $fileExt = strtolower(pathinfo($image_upload, PATHINFO_EXTENSION));
        $error = $_FILES["image_upload"]["error"];

        // valid image extensions
        $valid_extensions = array('jpeg', 'jpg', 'png', 'gif');

        if ($error > 0) {
            $exp = new CustomException("Error uploading image file.");
            $exp->sendBadRequest();
            exit(1);
        } else {

            // allow valid image file formats
            if (in_array($fileExt, $valid_extensions)) {

                $random_name = strtolower(rand(1000, 1000000) . "-" . $image_upload);
                $server_upload_name = $upload_dir . $random_name;
                $public_upload_url = "uploads/" . $random_name;
                $server_upload_name = preg_replace('/\s+/', '-', $server_upload_name);

                if (move_uploaded_file($image_tmp_name, $server_upload_name)) {
                    $image->path = "/" . $public_upload_url;
                    $image->type = $_POST["upload_type"];
                    $image->entity_id = $_POST["entity_id"];
                    $image->entity_type = $_POST["entity_type"];

                    // Create image
                    $new_image_id = $image->create();
                    if ($new_image_id) {
                        echo json_encode(
                            array('message' => 'File uploaded successfully', 'image_id' => $new_image_id)
                        );
                    } else {
                        $exp = new CustomException('Error uploading image file.');
                        $exp->sendBadRequest();
                        exit(1);
                    }
                } else {
                    $exp = new CustomException("Error uploading image file.");
                    $exp->sendBadRequest();
                    exit(1);
                }
            } else {
                $exp = new CustomException("Not a valid file format.");
                $exp->sendBadRequest();
                exit(1);
            }
        }
    } else {
        $exp = new CustomException("No file was sent.");
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
