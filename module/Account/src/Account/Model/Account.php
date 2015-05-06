<?php

namespace Account\Model;

class Account
{
    public $name;
    public $password;
    public $email;
    public $role;
    public $avatar;
    public $date_registered;

    public function exchangeArray($data)
    {
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->password(!empty($data['password'])) ? $data['password'] : null;
        $this->email(!empty($data['email'])) ? $data['email'] : null;
        $this->role(!empty($data['role'])) ? $data['role'] : 0;
        $this->avatar(!empty($data['avatar'])) ? $data['avatar'] : null;

        $date = new \DateTime();
        $this->date_registered(!empty($data['date_registered'])) ? $data['date_registered'] : $date->format('Y-m-d H:i:s');
    }
}