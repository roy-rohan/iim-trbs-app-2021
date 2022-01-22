<?php
include_once __DIR__ . "/MailTemplate.php";
include_once __DIR__ . "/../LaminasMail.php";

class UserAutoProvision extends LaminasMail
{
    private $userAccountPassword;

    use Template;
    private function prepareAndSendMail()
    {
        // $this->setTo("royrohan972@gmail.com", "Rancho");
        // $this->setFrom("royrohan1707@gmail.com", "TRBS");
        $this->setSubject("Account Created Successfully");
        $this->setBody($this->getTemplate($this->userAccountPassword));
    }

    public function setUserPassword($password)
    {
        $this->userAccountPassword = $password;
    }

    public function sendMail()
    {
        $this->prepareAndSendMail();
        $this->send();
    }
}
