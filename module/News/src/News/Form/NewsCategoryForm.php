<?php

namespace News\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class NewsCategoryForm extends Form
{
    public function __construct()
    {
        parent::__construct('newscategory');

        $this->add([
            'name' => 'id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name'       => 'name',
            'type'       => 'Text',
            'options'    => [
                'label' => 'Name',
            ],
            'attributes' => [
                'required' => 'required',
                'class'    => 'form-control',
            ],
        ]);

        $this->add([
            'name'       => 'path',
            'type'       => 'File',
            'options'    => [
                'label' => 'Image',
            ],
            'attributes' => [
                'required' => 'required',
                'class'    => 'form-control',
            ],
        ]);

        $this->add([
            'name'       => 'submit',
            'type'       => 'Submit',
            'attributes' => [
                'value' => 'Apply',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}
