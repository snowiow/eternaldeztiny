<?php

namespace News\Form;

use Zend\Form\Form;

class CommentForm extends Form
{
    public function __construct()
    {
        parent::__construct('comment');

        $this->add([
            'name' => 'account_id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'news_id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'date_posted',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name'       => 'content',
            'type'       => 'TextArea',
            'attributes' => [
                'class'       => 'form-control',
                'required'    => 'required',
                'rows'        => 5,
                'placeholder' => 'Write a comment...',
            ],
        ]);

        $this->add([
            'name'       => 'submit',
            'type'       => 'Submit',
            'attributes' => [
                'value' => 'Comment',
                'id'    => 'submitbutton',
                'class' => 'btn btn-success',
            ],
        ]);
    }
}
