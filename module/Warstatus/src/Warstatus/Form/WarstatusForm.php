<?php

namespace Warstatus\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class WarstatusForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('warstatus');

        $this->add([
            'name' => 'id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'account_id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'opted_out_date',
            'type' => 'Hidden',
        ]);

        $dateNow    = new \DateTime();
        $dateNowStr = $dateNow->format('Y-m-d');
        $dateMax    = $dateNow->add(new \DateInterval('P3M'));
        $dateMaxStr = $dateMax->format('Y-m-d');

        $this->add([
            'name'       => 'opted_in_date',
            'type'       => 'Zend\Form\Element\Date',
            'options'    => [
                'label'  => 'Opt-In date:',
                'format' => 'Y-m-d',
            ],
            'attributes' => [
                'class' => 'form-control',
                'min'   => $dateNowStr,
                'max'   => $dateMaxStr,
            ],
        ]);

        $this->add([
            'name'       => 'reason',
            'type'       => 'Text',
            'options'    => [
                'label' => 'Reason for opting out:',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name'       => 'gemable',
            'type'       => 'Checkbox',
            'options'    => [
                'label' => 'Gemable:',
            ],
            'attributes' => [
                'class' => 'horizontal-form-cb',
            ],
        ]);

        $this->add([
            'name'       => 'crusade',
            'type'       => 'Checkbox',
            'options'    => [
                'label' => 'Able for crusade:',
            ],
            'attributes' => [
                'class' => 'horizontal-form-cb',
            ],
        ]);

        $this->add([
            'name'       => 'submit',
            'type'       => 'Submit',
            'attributes' => [
                'id'    => 'submitbutton',
                'class' => 'btn btn-success',
                'value' => 'Edit',
            ],
        ]);
    }
}
