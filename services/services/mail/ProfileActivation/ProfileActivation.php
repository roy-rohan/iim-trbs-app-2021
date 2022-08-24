<?php
include_once __DIR__ . "/ProfileActivationMailTemplate.php";
include_once __DIR__ . "/../LaminasMail.php";

class ProfileActivation extends LaminasMail
{
    private $activationLink;
	private $name;

    use ProfileActivationMailTemplate;
    private function prepareAndSendMail()
    {
        // $this->setTo("royrohan972@gmail.com", "Rancho");
        // $this->setFrom("royrohan1707@gmail.com", "TRBS");
        $this->setSubject("TRBS - Account activation mail");
        $this->setBody($this->getProfileActivationTemplate($this->name, $this->activationLink));
    }

    public function setActivationLink($activationLink)
    {
        $this->activationLink = $activationLink;
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
