<?php

namespace Media\Form;

use Zend\Form\Form;

class MediaForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('media');

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
            'name' => 'account_id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name'    => 'url',
            'type'    => 'Text',
            'options' => [
                'label' => 'Youtube Url',
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
            ],
        ]);
    }
}
