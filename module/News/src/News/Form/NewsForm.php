<?php

namespace News\Form;

use Zend\Form\Form;

class NewsForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('news');

        $this->add([
            'name' => 'id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name'       => 'title',
            'type'       => 'Text',
            'options'    => [
                'label' => 'Title',
            ],
            'attributes' => [
                'class'    => 'form-control',
                'required' => 'required',
            ],

        ]);

        $this->add([
            'name' => 'account_id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name'       => 'category_id',
            'type'       => 'Select',
            'options'    => [
                'label'         => 'Category',
                'value_options' => [
                    0 => 'None',
                ],
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name'       => 'content',
            'type'       => 'Text',
            'options'    => [
                'label' => 'Text',
            ],
            'attributes' => [
                'class'    => 'form-control',
                'required' => 'required',
                'rows'     => 10,
            ],
        ]);

        $this->add([
            'name' => 'date_posted',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name'       => 'submit',
            'type'       => 'Submit',
            'attributes' => [
                'value' => 'Add',
                'id'    => 'submitbutton',
                'class' => 'btn btn-success',
            ],
        ]);
    }
}
