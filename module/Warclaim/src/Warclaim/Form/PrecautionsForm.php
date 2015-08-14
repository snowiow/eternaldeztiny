<?php

namespace Warclaim\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class PrecautionsForm extends Form
{

    public function __construct()
    {
        parent::__construct('warclaim');

        $this->add([
            'name'       => 'opponent',
            'type'       => 'Text',
            'options'    => [
                'label' => 'Opponent',
            ],
            'attributes' => [
                'class'    => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name'       => 'size',
            'type'       => 'Select',
            'options'    => [
                'label'         => 'Size',
                'value'         => '10',
                'value_options' => [
                    '10' => '10 vs 10',
                    '15' => '15 vs 15',
                    '20' => '20 vs 20',
                    '25' => '25 vs 25',
                    '30' => '30 vs 30',
                    '35' => '35 vs 35',
                    '40' => '40 vs 40',
                    '45' => '45 vs 45',
                    '50' => '50 vs 50',
                ],
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name'       => 'submit',
            'type'       => 'Submit',
            'attributes' => [
                'value' => 'Continue',
                'id'    => 'submitbutton',
                'class' => 'btn btn-success',
            ],
        ]);
    }
}
