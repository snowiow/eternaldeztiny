<?php

namespace ApplyNow\Form;

use Zend\Form\Form;

class ApplicationForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('application');

        $this->add([
            'name' => 'id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name'       => 'name',
            'type'       => 'Text',
            'options'    => [
                'label' => 'Ingame Name*',
            ],
            'attributes' => [
                'class'    => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name'       => 'tag',
            'type'       => 'Text',
            'options'    => [
                'label' => 'Ingame Tag*',
            ],
            'attributes' => [
                'class'    => 'form-control',
                'required' => 'required',
            ],

        ]);

        $this->add([
            'name'       => 'email',
            'type'       => 'Zend\Form\Element\Email',
            'options'    => [
                'label' => 'E-Mail Adress*',
            ],
            'attributes' => [
                'class'    => 'form-control',
                'type'     => 'email',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name'       => 'age',
            'type'       => 'Zend\Form\Element\Text',
            'options'    => [
                'label' => 'Age*',
            ],
            'attributes' => [
                'class'    => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name'       => 'th',
            'type'       => 'Zend\Form\Element\Select',
            'options'    => [
                'label'         => 'Town-Hall Level*',
                'value'         => '8',
                'value_options' => [
                    '1'  => '1',
                    '2'  => '2',
                    '3'  => '3',
                    '4'  => '4',
                    '5'  => '5',
                    '6'  => '6',
                    '7'  => '7',
                    '8'  => '8',
                    '9'  => '9',
                    '10' => '10',
                ],
            ],
            'attributes' => [
                'class'    => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name'       => 'warStars',
            'type'       => 'Zend\Form\Element\Text',
            'options'    => [
                'label' => 'Current war stars(War-Hero Archivement)*',
            ],
            'attributes' => [
                'class'    => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name'       => 'niceAndTidy',
            'type'       => 'Zend\Form\Element\Text',
            'options'    => [
                'label' => 'Current count in the nice and tidy archievement*',
            ],
            'attributes' => [
                'class'    => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name'       => 'goldGrab',
            'type'       => 'Zend\Form\Element\Text',
            'options'    => [
                'label' => 'Current count in the gold grab archievement*',
            ],
            'attributes' => [
                'class'    => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name'       => 'spoilsOfWar',
            'type'       => 'Zend\Form\Element\Text',
            'options'    => [
                'label' => 'Current count in the spoils of war archievement*',
            ],
            'attributes' => [
                'class'    => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name'       => 'infos',
            'type'       => 'TextArea',
            'options'    => [
                'label' => 'Tell us something about you',
            ],
            'attributes' => [
                'class' => 'form-control',
                'rows'  => 10,
            ],
        ]);

        $this->add([
            'name'       => 'strategies',
            'type'       => 'TextArea',
            'options'    => [
                'label' => 'Tell us about your war strategies.',
            ],
            'attributes' => [
                'class' => 'form-control',
                'rows'  => 10,
            ],
        ]);

        $this->add([
            'name'       => 'why',
            'type'       => 'TextArea',
            'options'    => [
                'label' => 'Why do you want to join us?',
            ],
            'attributes' => [
                'class' => 'form-control',
                'rows'  => 10,
            ],
        ]);

        $this->add([
            'name'       => 'basepic',
            'type'       => 'Zend\Form\Element\File',
            'options'    => [
                'label' => 'A Pic of your current warbase*',
            ],
            'attributes' => [
                'class'    => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name'       => 'profilepic',
            'type'       => 'Zend\Form\Element\File',
            'options'    => [
                'label' => 'A Pic of your Profile, where we see your troop levels.*',
            ],
            'attributes' => [
                'class'    => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name'       => 'processed',
            'type'       => 'Zend\Form\Element\Select',
            'options'    => [
                'label'         => 'Status',
                'value_options' => [
                    '0' => 'not processed',
                    '1' => 'accepted',
                    '2' => 'declined',
                ],
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name'       => 'submit',
            'type'       => 'Submit',
            'attributes' => [
                'id'    => 'submitbutton',
                'class' => 'btn btn-success',
            ],
        ]);
    }
}
