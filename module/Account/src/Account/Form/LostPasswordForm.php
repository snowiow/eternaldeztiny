<?php

namespace Account\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class LostPasswordForm extends Form
{
    public function __construct()
    {
        parent::__construct('lostpassword');

        $this->add([
            'name'       => 'email',
            'type'       => 'Email',
            'options'    => [
                'label' => 'The E-Mail adress of your account',
            ],
            'attributes' => [
                'class'    => 'form-control',
                'type'     => 'email',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name'       => 'submit',
            'type'       => 'Submit',
            'attributes' => [
                'id'    => 'submitbutton',
                'class' => 'btn btn-danger',
                'value' => 'Reset Password',
            ],
        ]);
    }

}
