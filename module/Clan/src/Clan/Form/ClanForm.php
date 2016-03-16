<?php

namespace Clan\Form;

use Zend\Form\Form;

class ClanForm extends Form
{
    public function __construct()
    {
        parent::__construct('clan');

        $this->add(
            [
                'name' => 'id',
                'type' => 'Hidden',
            ]
        );

        $this->add(
            [
                'name'       => 'tag',
                'type'       => 'Text',
                'options'    => [
                    'label' => 'Clan-Tag',
                ],
                'attributes' => [
                    'class'    => 'form-control',
                    'required' => 'required',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'name',
                'type' => 'Hidden',
            ]
        );

        $this->add(
            [
                'name'        => 'submit',
                'type'        => 'submit',
                'attribtutes' => [
                    'value' => 'Add',
                    'class' => 'btn btn-success',
                ],
            ]
        );
    }
}
