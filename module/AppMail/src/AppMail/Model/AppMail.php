<?php

namespace AppMail\Model;

use AppMail\Service\AppMailServiceInterface;

class AppMail implements AppMailInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $login;

    /**
     * @var string
     */
    protected $password;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = id;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host) {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getLogin() {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin($login) {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param string $pw
     */
    public function setPassword($pw) {
        $this->password = $pw;
    }

}