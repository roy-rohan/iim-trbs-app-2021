<?php
include __DIR__ . '/AbstractMail.php';
include_once __DIR__ . "/../../utilities/logger.php";

use Laminas\Mail;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;

class LaminasMail implements AbstractMail
{

    use Logger;

    public $mail;
    public $transport;

    public function __construct()
    {
        $this->mail = new Mail\Message();
        // $this->transport = new Mail\Transport\Sendmail();
        $this->transport = new SmtpTransport();
        $options   = new SmtpOptions([
			'name' => 'localhost',
            'host' => 'smtp.gmail.com',
            'port' => 465,
            'connection_class' => 'login',
            'connection_config' =>  [
                'username' => 'trbs2019@iima.ac.in', 'password' => 'redbrick2019', 'ssl' => 'ssl'
            ],
        ]);
        $this->transport->setOptions($options);
    }

    public function setTo($email, $receipientName)
    {
        $this->mail->addTo($email, $receipientName);
    }

    public function setFrom($from, $senderName)
    {
        $this->mail->setFrom($from, $senderName);
    }

    public function setSubject($subject)
    {
        $this->mail->setSubject($subject);
    }

    public function setBody($body)
    {
		$this->mail->getHeaders()->addHeaderLine(
            'Content-Type',
            'text/html; charset=ISO-8859-1'
        );
        $this->mail->setBody($body);
    }

    public function send()
    {
        try {
             $this->transport->send($this->mail);
			} catch (Exception $e) {
            $message = 'Mail Exception: ' . $e->getMessage();
            $this->messageLogger($message);
        }
    }
}
