<?php

namespace AppMail\Service;

interface AppMailServiceInterface
{
    public function sendMail($to, $subject, $content);
}