<?php

namespace Media\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Media implements InputFilterAwareInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $account_id;

    /**
     * NOTE: still needed because will be filled with name for the view
     * @var string
     */
    private $author;

    /**
     * @var int
     */
    private $title;

    /**
     * @var strin
     */
    private $url;

    /**
     * @var string
     */
    private $date_posted;

    /**
     * @var InputFilter
     */
    private $inputFilter;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAccountId()
    {
        return $this->account_id;
    }

    /**
     * @param string $creator
     */
    public function setAccountId($account_id)
    {
        $this->account_id = $account_id;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getDatePosted()
    {
        return $this->date_posted;
    }

    /**
     * @param string $date_posted
     */
    public function setDatePosted($date_posted)
    {
        $this->date_posted = $date_posted;
    }

    /**
     * Fills up the model class with the given data
     * @param array $data
     */
    public function exchangeArray($data)
    {
        $this->id         = (!empty($data['id'])) ? $data['id'] : null;
        $this->account_id = (!empty($data['account_id'])) ? $data['account_id'] : null;
        $this->title      = (!empty($data['title'])) ? $data['title'] : null;
        $this->url        = (!empty($data['url'])) ? $data['url'] : null;

        $date              = new \DateTime();
        $this->date_posted = (!empty($data['date_posted'])) ?
        $data['date_posted'] : $date->format('Y-m-d H:i:s');
    }

    /**
     *
     * @return array MediaModel as array
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

            $inputFilter->add([
                'name'     => 'id',
                'required' => true,
                'filters'  => [
                    ['name' => 'Int'],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'account_id',
                'required' => true,
                'filters'  => [
                    ['name' => 'Int'],
                ],
            ]);

            $inputFilter->add([
                'name'       => 'title',
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
                            'min'      => 3,
                            'max'      => 100,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'       => 'url',
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
                            'min'      => 12,
                            'max'      => 256,
                        ],
                        'name'    => 'Regex',
                        'options' => [
                            'pattern' => '/(https:\/\/)?(www\.)?youtube\.com\/watch\?.*v=([a-zA-Z0-9]+)/',
                        ],
                    ],
                ],
            ]);
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

}
