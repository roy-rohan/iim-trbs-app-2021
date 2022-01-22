<?php
include_once __DIR__ . "/MailTemplate.php";
include_once __DIR__ . "/../LaminasMail.php";

class ForgotPassword extends LaminasMail
{
    private $passwordChangeLink;

    use Template;
    private function prepareAndSendMail()
    {
        $this->setSubject("TRBS - Account Password Change Link");
        $this->setBody($this->getTemplate($this->passwordChangeLink));
    }

    public function setPasswordChangeLink($link)
    {
        $this->passwordChangeLink = $link;
    }

    public function sendMail()
    {
        $this->prepareAndSendMail();
        $this->send();
    }
}
