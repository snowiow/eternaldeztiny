<?php

namespace Warlog\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

class Warlog implements InputFilterAwareInterface
{
    /**
     * @var InputFilter
     */
    private $inputFilter;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $wins;

    /**
     * @var int
     */
    private $losses;

    /**
     * @var int
     */
    private $draws;

    private $file;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getWins()
    {
        return $this->wins;
    }

    /**
     * @param int $wins
     */
    public function setWins(int $wins)
    {
        $this->wins = $wins;
    }

    /**
     * @return int
     */
    public function getLosses()
    {
        return $this->losses;
    }

    /**
     * @param int $losses
     */
    public function setLosses(int $losses)
    {
        $this->losses = $losses;
    }

    /**
     * @return int
     */
    public function getDraws()
    {
        return $this->draws;
    }

    /**
     * @param int $draws
     */
    public function setDraws(int $draws)
    {
        $this->draws = $draws;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Fills up the model class with the given data
     * @param array $data The warlog data needed to fill the model
     */
    public function exchangeArray(array $data)
    {
        $this->id     = !empty($data['id']) ? $data['id'] : null;
        $this->wins   = !empty($data['wins']) ? $data['wins'] : 0;
        $this->losses = !empty($data['losses']) ? $data['losses'] : 0;
        $this->draws  = !empty($data['draws']) ? $data['draws'] : 0;
        $this->file   = !empty($data['file']) ? $data['file'] : null;
    }

    /**
     * @return array WarlogModel as array
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

    /**
     * Creates the InputFilter for the warlog form.
     * @return \Zend\InputFilter\InputFilter
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name'       => 'wins',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 5,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'       => 'draws',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 5,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'       => 'losses',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 5,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'file',
                'required' => false,
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
