<?php
include_once __DIR__ . "/ForgotPasswordMailTemplate.php";
include_once __DIR__ . "/../LaminasMail.php";

class ForgotPassword extends LaminasMail
{
    private $link;
    private $name;

    use ForgotPasswordMailTemplate;

    private function prepareAndSendMail()
    {
        $this->setSubject("TRBS - Account Password Change Link");
        $this->setBody($this->getForgotPasswordMailTemplate($this->link));
    }

    public function setActionLink($link)
    {
        $this->link = $link;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function sendMail()
    {
        $this->prepareAndSendMail();
        $this->send();
    }
}
