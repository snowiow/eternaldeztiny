<?php

namespace Warclaim\Form;

use Zend\Form\Form;

class WarclaimForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('warclaim');

        for ($i = 0; $i < 50; $i++) {
            $this->add([
                'name'    => $i,
                'type'    => 'Text',
                'options' => [
                    'label' => ($i + 1) . '.',

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
