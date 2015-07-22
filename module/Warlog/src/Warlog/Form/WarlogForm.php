<?php

namespace Warlog\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class WarlogForm extends Form
{

    /**
     * @param null $name
     */
    public function __construct($name = null)
    {
        parent::__construct('warlog');

        $this->add([
            'name' => 'id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name'    => 'wins',
            'type'    => 'Zend\Form\Element\Text',
            'options' => [
                'label' => 'Wins',
            ],
        ]);

        $this->add([
            'name'    => 'losses',
            'type'    => 'Zend\Form\Element\Text',
            'options' => [
                'label' => 'Losses',
            ],
        ]);

        $this->add([
            'name'    => 'draws',
            'type'    => 'Zend\Form\Element\Text',
            'options' => [
                'label' => 'Draws',
            ],
        ]);

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
