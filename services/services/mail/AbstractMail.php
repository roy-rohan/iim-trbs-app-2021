<?php

interface AbstractMail
{
    public function setTo($to, $receipientName);
    public function setFrom($from, $senderName);
    public function setSubject($subject);
    public function setBody($body);
    public function send();
}
