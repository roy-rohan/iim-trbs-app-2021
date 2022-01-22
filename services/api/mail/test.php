<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . "/../../services/mail/ProfileActivation/ProfileActivation.php";


$mail = new ProfileActivation();
$mail->setTo("royrohan972@gmail.com", "Rancho");
$mail->setFrom("royrohan1707@gmail.com", "TRBS");
$mail->setActivationLink("www.google.com");
$mail->sendMail();
