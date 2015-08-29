<?php

namespace Account\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class ResetPasswordForm extends Form
{

    public function __construct()
    {
        parent::__construct('resetpassword');

        $this->add([
            'name' => 'userhash',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name'       => 'password',
            'type'       => 'Password',
            'options'    => [
                'label' => 'New Password',
            ],
            'attributes' => [
                'class'    => 'form-control',
                'type'     => 'password',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name'       => 'repeat',
            'type'       => 'Password',
            'options'    => [
                'label' => 'Repeat Password',
            ],
            'attributes' => [
                'class'    => 'form-control',
                'type'     => 'password',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name'       => 'submit',
            'type'       => 'Submit',
            'attributes' => [
                'class' => 'btn btn-success',
                'value' => 'Reset Password',
            ],
        ]);
    }
}
