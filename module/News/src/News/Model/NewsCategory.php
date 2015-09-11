<?php

namespace News\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class NewsCategory implements InputFilterAwareInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $path;

    /**
     * @var InputFilter
     */
    private $inputFilter;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Fill up the model class with the given data
     * @param array $data
     */
    public function exchangeArray($data)
    {
        $this->id   = !empty($data['id']) ? $data['id'] : null;
        $this->name = !empty($data['name']) ? $data['name'] : null;
        $this->path = !empty($data['path']) ? $data['path'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterAwareInterface $inputFIlter)
    {
        throw new \Exception("not used");
    }

    public function getInputFilter()
    {
        return;
    }
}
