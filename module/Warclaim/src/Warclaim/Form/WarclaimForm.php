<?php

namespace Warclaim\Form;

use Zend\Form\Form;

class WarclaimForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('warclaim');

        $this->add([
            'name' => 'id',
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

        for ($i = 0; $i < 50; $i++) {
            $this->add([
                'name'       => $i,
                'type'       => 'Zend\Form\Element\Text',
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
