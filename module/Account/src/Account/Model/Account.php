<?php

namespace Account\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

use Application\Constants;

class Account
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $userhash;

    /**
     * @var string
     */
    private $email;

    /**
     * @var int
     */
    private $role;

    /**
     * @var string
     */
    private $avatar;

    /**
     * @var string
     */
    private $date_registered;

    /**
     * @var InputFilter
     */
    protected $inputFilter;

    /**
     * @param int
     */
    public function getId() {
        return $this->id;
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
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getUserHash() {
        return $this->userhash;
    }

    /**
     * @param string $hash
     */
    public function setUserHash($hash) {
        $this->userhash = $hash;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * @param int $role
     */
    public function setRole($role) {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getAvatar() {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar($avatar) {
        $this->avatar = $avatar;
    }

    /**
     * @return string
     */
    public function getDateRegistered() {
        return $this->date_registered;
    }

    /**
     * Fills up the model class with the given data
     * @param array $data The account data needed to fill the model
     */
    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->name = !empty($data['name']) ? $data['name'] : null;
        $this->password = !empty($data['password']) ? hash('sha256', $data['password']) . Constants::SALT  : null;
        $this->userhash = hash('sha256', $this->name);
        $this->email = !empty($data['email']) ? $data['email'] : null;
        $this->role = !empty($data['role']) ? $data['role'] : 0;
        $this->avatar = !empty($data['avatar']) ? $data['avatar'] : null;

        $date = new \DateTime();
        $this->date_registered = !empty($data['date_registered']) ? $data['date_registered'] :
            $date->format('Y-m-d H:i:s');
    }

    /**
     * @return array AccountModel as array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Always throws exception. InputFilter wont be set from outside.
     * @param \Zend\InputFilter\InputFilterInterface $inputFilter
     *
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * Creates the InputFilter for an AccountModel.
     * @return \Zend\InputFilter\InputFilter
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name' => 'name',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 64,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'password',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 4,
                            'max' => 64,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'email',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 4,
                            'max' => 255,
                        ],
                    ],
                ],
            ]);
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}