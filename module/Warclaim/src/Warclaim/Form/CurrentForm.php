<?php

namespace Warclaim\Form;

use Warclaim\Form\CreateForm;

class CurrentForm extends CreateForm
{

    public function __construct($size)
    {
        parent::__construct($size);

        for ($i = 0; $i < $size; $i++) {
            $this->add([
                'name'       => $i . 'c',
                'type'       => 'Text',
                'attributes' => [
                    'class' => 'form-control',
                ],
            ]);
            $this->add([
                'name'       => $i . 'i',
                'type'       => 'Text',
                'attributes' => [
                    'class' => 'form-control',
                ],
            ]);
        }
        $this->get('submit')->setAttribute('value', 'Update');
    }
}
