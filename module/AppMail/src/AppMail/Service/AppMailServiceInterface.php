<?php

namespace AppMail\Service;

interface AppMailServiceInterface
{
    public function sendMail(string $to, string $subject, string $content, array $files = []);
}
