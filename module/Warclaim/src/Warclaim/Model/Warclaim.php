<?php

namespace Warclaim\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilter;

class Warclaim
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $size;

    /**
     * @var string
     */
    private $opponent;

    /**
     * @var string
     */
    private $strategy;

    /**
     * @var string
     */
    private $assignments;

    /**
     * @var bool
     */
    private $open;

    private $createInputFilter;

    private $precautionsInputFilter;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getOpponent()
    {
        return $this->opponent;
    }

    /**
     * @param string $opponent
     */
    public function setOpponent($opponent)
    {
        $this->opponent = $opponent;
    }

    public function getAssignments()
    {
        return $this->assignments;
    }

    public function setAssignments($assignments)
    {
        $this->assignments = $assignments;
    }

    public function getStrategy()
    {
        return $this->strategy;
    }

    public function setStrategy($strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @return bool
     */
    public function isOpen()
    {
        return $this->open;
    }

    /**
     * @param bool $open
     */
    public function setOpen($open)
    {
        $this->open = $open;
    }

    public function exchangeArray($data)
    {
        $this->id       = (!empty($data['id'])) ? $data['id'] : null;
        $this->size     = (!empty($data['size'])) ? $data['size'] : null;
        $this->strategy = (!empty($data['strategy'])) ? $data['strategy'] : null;
        $this->opponent = (!empty($data['opponent'])) ? $data['opponent'] : null;

        $assignments = [];
        //Comes from db
        if (array_key_exists('assignments', $data)) {
            $assignments = unserialize($data['assignments']);
        } else {
            for ($i = 0; $i < $this->size; $i++) {
                $assignments[$i] = $data[$i] ? $data[$i] : '';
            }
        }
        $this->assignments = $assignments;
        $this->open        = (!empty($data['open'])) ? $data['open'] : true;
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

    public function getPrecautionsInputFilter()
    {
        if (!$this->precautionsInputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name'       => 'opponent',
                'filter'     => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 3,
                            'max'      => 255,
                        ],
                    ],
                ],
            ]);
            $this->precautionsInputFilter = $inputFilter;
        }
        return $this->precautionsInputFilter;
    }

    public function getCreateInputFilter($size)
    {
        if (!$this->createInputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name'       => 'strategy',
                'filter'     => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'max'      => 500,
                        ],
                    ],
                ],
            ]);

            //50 is maximum possible size in clanwars
            for ($i = 0; $i < $size; $i++) {
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
            $this->createInputFilter = $inputFilter;
        }
        return $this->createInputFilter;
    }
}
