<?php

namespace News\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilter;

class Comment implements InputFilterAwareInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $newsId;

    /**
     * @var int
     */
    private $accountId;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $datePosted;

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
     * @return int
     */
    public function getNewsId()
    {
        return $this->newsId;
    }

    /**
     * @param int $newsId
     */
    public function setNewsId(int $newsId)
    {
        $this->newsId = $newsId;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param int $accountId
     */
    public function setAccountId(int $accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getDatePosted()
    {
        return $this->datePosted;
    }

    /**
     * @param string $datePosted
     */
    public function setDatePosted(string $datePosted)
    {
        $this->value = $datePosted;
    }

    public function exchangeArray($data)
    {
        $this->id         = (!empty($data['id'])) ? $data['id'] : null;
        $this->newsId     = (!empty($data['news_id'])) ? $data['news_id'] : null;
        $this->accountId  = (!empty($data['account_id'])) ? $data['account_id'] : null;
        $this->content    = (!empty($data['content'])) ? $data['content'] : null;
        $date             = new \DateTime();
        $this->datePosted = (!empty($data['date_posted'])) ? $data['date_posted'] : $date->format('Y-m-d H:i:s');
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception('not used');
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name'     => 'news_id',
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
                'name'       => 'content',
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
                            'min'      => 5,
                            'max'      => 512,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'date_posted',
                'required' => false,
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
