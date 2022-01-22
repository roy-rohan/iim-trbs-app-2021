<?php

trait Template
{
    public function getTemplate($password)
    {
        $template =
            'Your TRBS account has been created successfully. Your account password: ' . $password;
        return $template;
    }
}
