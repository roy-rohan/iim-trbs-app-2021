<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../auth/token_utils.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Get raw posted data


    $data = json_decode(file_get_contents("php://input"));
    $user_data = authenicateRequest($data->auth_token);

    $paymentInfo = json_decode(dataDecode($data->payment_token));

    $url = "https://payments.iima.ac.in/online/api/v1/transaction";
    try {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Content-Type: application/x-www-form-urlencoded",
            "Authorization: Basic dXNlcjp1c2VyQDEyMw=="
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $purposeId = 124;

        $params = "name=$paymentInfo->name&purpose_id=$purposeId"
            . "&email=$paymentInfo->email&mobile=$paymentInfo->mobile"
            . "&description=$paymentInfo->description&amount=$paymentInfo->amount"
            . "&acctype=domestic";

        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);

        $json = json_decode($response);
        if ($json->errorcode == 200) {
            echo $json->response;
        } else if ($json->errorcode == 400) {
            // message_logger($json);
            $exp = new CustomException("Failed to initate payment. Please contact the system administrator.");
            $exp->sendBadRequest();
        } else {
            // $exp = new CustomException($json->errorcode . "Something went wrong." . $json->response);
            $exp = new CustomException("Payment server is currently down. Please contact the system administrator.");
            $exp->sendBadRequest();
        }

        curl_close($curl);
    } catch (Exception $exp) {
        $exp = new CustomException("Something went wrong. Error Occurred.");
        $exp->sendBadRequest();
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
