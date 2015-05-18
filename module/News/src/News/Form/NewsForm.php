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
            'name'    => 'title',
            'type'    => 'Text',
            'options' => [
                'label' => 'Title',
            ],
        ]);

        $this->add([
            'name'    => 'account_id',
            'type'    => 'Hidden',
        ]);

        $this->add([
            'name'    => 'content',
            'type'    => 'Text',
            'options' => [
                'label' => 'Text'
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
                'value' => 'Go',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}
