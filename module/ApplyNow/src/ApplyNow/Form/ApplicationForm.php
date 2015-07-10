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
            'name'    => 'name',
            'type'    => 'Text',
            'options' => [
                'label' => 'Ingame Name*',
            ],
        ]);

        $this->add([
            'name'    => 'tag',
            'type'    => 'Text',
            'options' => [
                'label' => 'Ingame Tag*',
            ],
        ]);

        $this->add([
            'name'    => 'email',
            'type'    => 'Zend\Form\Element\Email',
            'options' => [
                'label' => 'E-Mail Adress*',
            ],
        ]);

        $this->add([
            'name'    => 'age',
            'type'    => 'Zend\Form\Element\Text',
            'options' => [
                'label' => 'Age*',
            ],
        ]);

        $this->add([
            'name'    => 'th',
            'type'    => 'Zend\Form\Element\Select',
            'options' => [
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
        ]);

        $this->add([
            'name'    => 'warStars',
            'type'    => 'Zend\Form\Element\Text',
            'options' => [
                'label' => 'Current war stars(War-Hero Archivement)*',
            ],
        ]);

        $this->add([
            'name'    => 'infos',
            'type'    => 'Text',
            'options' => [
                'label' => 'Tell us something about you',
            ],
        ]);

        $this->add([
            'name'    => 'strategies',
            'type'    => 'Text',
            'options' => [
                'label' => 'Tell us about your war strategies. With whom are you familiar with, what do you want to learn in the near future.',
            ],
        ]);

        $this->add([
            'name'    => 'why',
            'type'    => 'Text',
            'options' => [
                'label' => 'Why do you want to join us?',
            ],
        ]);

        $this->add([
            'name'    => 'basePic',
            'type'    => 'Zend\Form\Element\File',
            'options' => [
                'label' => 'A Pic of your current warbase*',
            ],
        ]);

        $this->add([
            'name'    => 'profilePic',
            'type'    => 'Zend\Form\Element\File',
            'options' => [
                'label' => 'A Pic of your Profile, where we see your troop levels.*',
            ],
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
