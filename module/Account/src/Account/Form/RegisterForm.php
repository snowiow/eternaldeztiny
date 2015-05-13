<?php

namespace Account\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class RegisterForm extends Form
{

    /**
     * @param null $name
     */
    public function __construct($name = null)
    {
        parent::__construct('register');

        $this->add([
            'name'       => 'name',
            'type'       => 'Text',
            'options'    => [
                'label' => 'Username',
            ],
        ]);

        $this->add([
            'name'       => 'password',
            'type'       => 'Password',
            'options'    => [
                'label' => 'Password',
            ],
        ]);

        $this->add([
            'name' => 'email',
            'type' => 'Zend\Form\Element\Email',
            'options' => [
                'label' => 'E-Mail Adress',
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