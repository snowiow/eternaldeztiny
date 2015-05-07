<?php

namespace Account\Form;

use Zend\Form\Form;
use Zend\Captcha\AdapterInterface as CaptchaAdapter;
use Zend\Form\Element;

class RegisterForm extends Form
{
    protected  $captcha;

    public function __construct(CaptchaAdapter $captcha)
    {
        parent::__construct('register');
        $this->captcha = $captcha;

        $this->add([
            'name'       => 'name',
            'type'       => 'Text',
            'options'    => [
                'Label' => 'Username',
            ],
        ]);

        $this->add([
            'name'       => 'password',
            'type'       => 'Password',
            'options'    => [
                'Label' => 'Password',
            ],
        ]);

        $this->add([
            'name' => 'email',
            'type' => 'Zend\Form\Element\Email',
            'options' => [
                'Label' => 'E-Mail Adress',
            ],
        ]);

        $this->add([
            'name' => 'captcha',
            'type' => 'Zend\Form\Element\Captcha',
            'options' => [
                'Label' => 'Please verify that you are human',
                'captcha' => $this->captcha,
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'value' => 'Go',
                'id' => 'submitbutton',
            ],
        ]);
    }
}