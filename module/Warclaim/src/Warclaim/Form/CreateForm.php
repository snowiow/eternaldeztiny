<?php

namespace Warclaim\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class CreateForm extends Form
{
    public function __construct($size)
    {
        parent::__construct('create');

        $this->add([
            'name' => 'id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'size',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'opponent',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name'       => 'strategy',
            'type'       => 'TextArea',
            'options'    => [
                'label' => 'War Strategy',
            ],
            'attributes' => [
                'class' => 'form-control',
                'rows'  => 10,
            ],
        ]);

        for ($i = 0; $i < $size; $i++) {
            $this->add([
                'name'       => $i,
                'type'       => 'Text',
                'options'    => [
                    'label' => ($i + 1) . '.',
                ],
                'attributes' => [
                    'class'    => 'form-control',
                    'required' => 'required',
                ],
            ]);
        }

        $this->add([
            'name'       => 'submit',
            'type'       => 'Submit',
            'attributes' => [
                'value' => 'Start war!',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}
