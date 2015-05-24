<?php

namespace Account\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class UploadAvatarForm extends Form
{

    /**
     * @param null $name
     */
    public function __construct($name = null)
    {
        parent::__construct('uploadavatar');

        $this->add([
            'name'    => 'file',
            'type'    => 'Zend\Form\Element\File',
            'options' => [
                'label' => 'File Path',
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
