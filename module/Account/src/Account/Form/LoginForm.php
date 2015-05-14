<?php

namespace Account\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class LoginForm extends Form
{

    /**
     * @param null $name
     */
    public function __construct($name = null)
    {
        parent::__construct('login');

        $this->add([
            'name'    => 'name',
            'type'    => 'Text',
            'options' => [
                'label' => 'Username',
            ],
        ]);

        $this->add([
            'name'    => 'password',
            'type'    => 'Password',
            'options' => [
                'label' => 'Password',
            ],
        ]);

        $this->add([
            'name'       => 'submit',
            'type'       => 'Submit',
            'attributes' => [
                'value' => 'Go',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}