<?php

namespace Warclaim\Form;

class CurrentForm extends CreateForm
{

    public function __construct($size, $role)
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

        if ($role < \Account\Model\Role::CO) {
            $this->get('strategy')->setAttribute('readonly', 'readonly');

            for ($i = 0; $i < $size; $i++) {
                $this->get($i)->setAttribute('readonly', 'readonly');
            }
        }

        $this->get('submit')->setAttribute('value', 'Update');
    }
}
