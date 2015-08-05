<?php

namespace Account\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class EditProfileForm extends Form
{

    /**
     * @param null $name
     */
    public function __construct($name = null)
    {
        parent::__construct('editprofile');

        $this->add([
            'name'    => 'file',
            'type'    => 'Zend\Form\Element\File',
            'options' => [
                'label' => 'Avatar:',
            ],
        ]);

        $this->add([
            'name'    => 'mini',
            'type'    => 'Text',
            'options' => [
                'label' => 'Name of Mini-Account:',
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
