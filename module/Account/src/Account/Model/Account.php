<?php

namespace Account\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

use Application\Constants;

class Role
{
    const NOT_ACTIVATED = 0;
    const USER          = 1;
    const MEMBER        = 1 << 1;
    const ELDER         = 1 << 2;
    const CO            = 1 << 3;
    const LEADER        = 1 << 4;
    const ADMIN         = 1 << 5;
}

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
     * @var Role
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
    protected $registerInputFilter;

    /**
     * @var InputFilter
     */
    protected $loginInputFilter;

    /**
     * @var InputFilter
     */
    protected $uploadAvatarInputFilter;

    /**
     * @param int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getUserHash()
    {
        return $this->userhash;
    }

    /**
     * @param string $hash
     */
    public function setUserHash($hash)
    {
        $this->userhash = $hash;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param Role $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @return string
     */
    public function getDateRegistered()
    {
        return $this->date_registered;
    }

    /**
     * Fills up the model class with the given data
     * @param array $data The account data needed to fill the model
     */
    public function exchangeArray($data)
    {
        $this->id       = (!empty($data['id'])) ? $data['id'] : null;
        $this->name     = !empty($data['name']) ? $data['name'] : null;
        $this->password = !empty($data['password']) ? $data['password'] : null;
        $this->userhash = hash('sha256', $this->name);
        $this->email    = !empty($data['email']) ? $data['email'] : null;
        $this->role     = !empty($data['role']) ? $data['role'] : Role::NOT_ACTIVATED;
        $this->avatar   = !empty($data['avatar']) ? $data['avatar'] : null;

        $date                  = new \DateTime();
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
     * Creates the InputFilter for the registration form.
     * @return \Zend\InputFilter\InputFilter
     */
    public function getRegisterInputFilter()
    {
        if (!$this->registerInputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name'       => 'name',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 3,
                            'max'      => 64,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'       => 'password',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 4,
                            'max'      => 64,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'       => 'email',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 4,
                            'max'      => 255,
                        ],
                    ],
                ],
            ]);
            $this->registerInputFilter = $inputFilter;
        }
        return $this->registerInputFilter;
    }

    /**
     * Creates a InputFilter for the loginForm
     * @return \Zend\InputFilter\InputFilter
     */
    public function getLoginInputFilter()
    {
        if (!$this->loginInputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name'       => 'name',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'       => 'password',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 4,
                            'max'      => 64,
                        ],
                    ],
                ],
            ]);
            $this->loginInputFilter = $inputFilter;
        }
        return $this->loginInputFilter;
    }

    public function getUploadAvatarInputFilter()
    {
        if (!$this->uploadAvatarInputFilter) {
            $inputFilter = new InputFilter();
            $fileInput   = new InputFilter\FileInput('image-file');
            $fileInput->setRequired(true);
            $fileInput->getFilterChain()->attachByName(
                'filerenameupload',
                [
                    'target'    => './data/tmp/avatar.png',
                    'randomize' => true,
                ]
            );
            $inputFilter->add($fileInput);

            $this->uploadAvatarInputFilter = $inputFilter;
        }
        return $this->uploadAvatarInputFilter;
    }

    public static function convertToRole($int)
    {
        switch ($int) {
        case Role::USER:
            return 'User';
        case Role::MEMBER:
            return 'Member';
        case Role::ELDER:
            return 'Elder';
        case Role::CO:
            return 'Co-Leader';
        case Role::LEADER:
            return 'Leader';
        default:
            return 'Elder';
        }
    }
}
