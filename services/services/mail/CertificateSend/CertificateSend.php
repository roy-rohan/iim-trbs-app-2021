<?php
include_once __DIR__ . "/MailTemplate.php";
include_once __DIR__ . "/../LaminasMail.php";

class CertificateSend extends LaminasMail
{
    private $name;
    private $email;
    private $mobile;
    private $subject;
    private $message;

    use Template;
    private function prepareAndSendMail()
    {
        $this->setSubject($this->subject);
        $this->setBody($this->getTemplate());
    }
	
    public function setName($name)
    {
        $this->name = $name;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    public function setSubject($subject)
    {
		parent::setSubject($subject);
        $this->subject = $subject;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function sendMail()
    {
        $this->prepareAndSendMail();
        $this->send();
    }
}
