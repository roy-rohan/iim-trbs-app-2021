<?php

include_once __DIR__ . '/../../models/UserCertificate.php';
include_once __DIR__ . '/../../models/User/AppUser.php';
include_once __DIR__ . '/../../models/User/Order/Booking.php';
include_once __DIR__ . "/../../utilities/logger.php";
include_once __DIR__ . "/../mail/CertificateSend/CertificateSend.php";


class CertificateService
{
    static function sendToAllRegisteredUsers($db, $data) {
        //fetch user ids
        $app_user_model = new AppUser($db);
        
        $searchData = (object)array("filters" => array(), "filter_op" => "", "sort" => array());

        $result = $app_user_model->read($searchData);

        // Get row count
        $num = $result->rowCount();

        // Check if any user exists
        $app_user_id_arr = array();
        if ($num > 0) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                array_push($app_user_id_arr, array("user_id" => $app_user_id, "email" => $email_id));
            }
        }

        //create records in loop
        foreach ($app_user_id_arr as $user) {
            CertificateService::createEntryAndSendResponse($db,  $user["user_id"], $user["email"], $data->certificate_id);
        }
    }

    static function sendToAllParticipatedUsers($db, $data)
    {
        //fetch user ids
        $booking_model = new Booking($db);
        $searchData = (object)array("filters" => array(), "filter_op" => "", "sort" => array());

        $result = $booking_model->readDistinctUsers($searchData);

        // Get row count
        $num = $result->rowCount();

        // Check if any user exists
        $app_user_id_arr = array();
        if ($num > 0) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                array_push($app_user_id_arr, array("user_id" => $app_user_id, "email" => $email_id));
            }
        }

        //create records in loop
        foreach ($app_user_id_arr as $user) {
            CertificateService::createEntryAndSendResponse($db,  $user["user_id"], $user["email"], $data->certificate_id);
        }
    }

    static function sendToUsersByEmail($db, $data)
    {
        $emails = $data->emails;
        $emailQueryStr = "(";
        foreach ($emails as $email) {
            $emailQueryStr .= "'" . $email . "',";
        }
        if (strpos($emailQueryStr, ',')) {
            if (substr($emailQueryStr, -1) == ',') {
                $emailQueryStr = substr($emailQueryStr, 0, strlen($emailQueryStr) - 1);
            }
        }

        if($emailQueryStr == "(") {
            return;
        } else {
            $emailQueryStr .= ")";
        }

        //fetch user ids
        $app_user_model = new AppUser($db);

        $searchData = (object)array("filters" => array((object)array("field_name" => 'email_id', "value" => $emailQueryStr, "op" => "IN")), "filter_op" => "AND", "sort" => array());

        $result = $app_user_model->read($searchData);

        // Get row count
        $num = $result->rowCount();

        // Check if any user exists
        $app_user_id_arr = array();
        if ($num > 0) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                array_push($app_user_id_arr, array("user_id" => $app_user_id, "email" => $email_id));
            }
        }

        //create records in loop
        foreach ($app_user_id_arr as $user) {
            CertificateService::createEntryAndSendResponse($db, $user["user_id"], $user["email"], $data->certificate_id);
        }

    }

    static function createEntryAndSendResponse($db, $user_id, $user_email, $certificate_id)
    {
        try{
            $config = Laminas\Config\Factory::fromFile('../../auth/config.php', true);
            $mail = new CertificateSend();
            $mail->setSubject('Congratulations! You have received a certificate');
            $mail->setTo($user_email, '');
            $mail->setFrom($config->get('tenantEmailId'), $config->get('tenantEmailName'));
            $mail->sendMail();
        } catch (Exception $error) {
            message_logger($error);
        }
        // Instantiate userCertificate object
        $userCertificate = new UserCertificate($db);
        // Create userCertificate
        $userCertificate->create($user_id, $certificate_id);
    }

}
