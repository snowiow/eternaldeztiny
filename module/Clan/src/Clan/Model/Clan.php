<?php

namespace Clan\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilter;

class Clan implements InputFilterAwareInterface
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var tag
     */
    private $tag;

    /**
     * @var string
     */
    private $name;

    /**
     * @var InputFilter
     */
    private $inputFilter;

    /**
     * Returns the clan id
     * @return int $id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns the clan tag
     * @return string the clantag
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * Sets the clantag
     * @param string $tag the tag to be set
     * @return void
     */
    public function setTag(string $tag): string
    {
        $this->tag = $tag;
    }

    /**
     * Returns the clan name
     * @return string name of the clan
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the clan name
     * @param string $name the clan name to be set
     * @return void
     */
    public function setName(string $name): string
    {
        $this->name = $name;
    }

    /**
     * Fill the object with a data array
     * @param array $data the array which holds the data to be set
     * @return void
     */
    public function exchangeArray(array $data)
    {
        $this->id   = !empty($data['id']) ? $data['id'] : 0;
        $this->tag  = !empty($data['tag']) ? $data['tag'] : null;
        $this->name = !empty($data['name']) ? $data['name'] : null;
    }

    /**
     * Returns a copy of the object as an array structure
     * @return array the object as an array
     */
    public function getArrayCopy(): array
    {
        return get_object_vars($this);
    }

    /**
     * Always throws exception. InputFilter wont be set from outside.
     * @param \Zend\InputFilter\InputFilterInterface $inputFilter
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * Returns the InputFilter for a NewsModel.
     * @return InputFilter
     */
    public function getInputFilter(): InputFilter
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(
                [
                    'name'       => 'tag',
                    'required'   => true,
                    'filters'    => [
                        ['name' => 'StripTags'],
                        ['name' => 'stringTrim'],
                    ],
                    'validators' => [
                        [
                            'name'    => 'StringLength',
                            'options' => [
                                'encoding' => 'UTF-8',
                                'min'      => 1,
                                'max'      => 64,
                            ],
                        ],
                    ],
                ]
            );

            $inputFilter->add(
                [
                    'name'       => 'tag',
                    'required'   => true,
                    'filters'    => [
                        ['name' => 'StripTags'],
                        ['name' => 'stringTrim'],
                    ],
                    'validators' => [
                        [
                            'name'    => 'StringLength',
                            'options' => [
                                'encoding' => 'UTF-8',
                                'min'      => 1,
                                'max'      => 64,
                            ],
                        ],
                    ],
                ]
            );

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
