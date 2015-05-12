<?php

namespace AppMail\Model;

interface AppMailInterface
{
    public function getId();

    public function  getName();

    public function  getHost();

    public function getLogin();

    public function  getPassword();

}