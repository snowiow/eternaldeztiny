<?php

namespace Account\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class SearchUserForm extends Form
{

    public function __construct()
    {
        parent::__construct('searchuserform');

        $this->add([
            'name'       => 'name',
            'type'       => 'Text',
            'options'    => [
                'label' => 'Username',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name'       => 'role',
            'type'       => 'Select',
            'options'    => [
                'label'         => 'Role',
                'value_options' => [
                    0 => 'All',
                ]+\Account\Model\Role::getRoleArray(),
            ],
            'attributes' => [
                'class'    => 'form-control',
                'multiple' => 'multiple',
            ],
        ]);

        $this->add([
            'name'       => 'submit',
            'type'       => 'Submit',
            'attributes' => [
                'class' => 'btn btn-success',
                'value' => 'Search',
            ],
        ]);
    }
}
