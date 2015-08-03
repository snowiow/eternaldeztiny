<?php

namespace Warclaim\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilter;

class Warclaim implements InputFilterAwareInterface
{
    private $id;
    private $assignments;
    private $inputFilter;

    public function getId()
    {
        return $this->id;
    }

    public function getAssignments()
    {
        return $this->assignments;
    }

    public function setAssignments($assignments)
    {
        $this->assignments = $assignments;
    }

    public function exchangeArray($data)
    {

        $this->id          = (!empty($data['id'])) ? data['id'] : null;
        $this->assignments = (!empty($data['assignments'])) ? data['assignments'] : null;
    }

    /**
     *
     * @return array Warclaim Model as array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Always throws exception. InputFilter wont be set from outside.
     * @param \Zend\InputFilter\InputFilterInterface $inputFilter
     *
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            //50 is maximum possible size in clanwars
            for ($i = 0; $i < 50; $i++) {
                $inputFilter->add([
                    'name'       => $i,
                    'filters'    => [
                        ['name' => 'StripTags'],
                        ['name' => 'StringTrim'],
                    ],
                    'validators' => [
                        [
                            'name'    => 'StringLength',
                            'options' => [
                                'encoding' => 'UTF-8',
                                'min'      => 3,
                                'max'      => 64,
                            ],
                            'name'    => 'Regex',
                            'options' => [
                                'pattern' => '/^[a-zA-Z0-9_-]+/',
                            ],
                        ],
                    ],
                ]);
            }
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
