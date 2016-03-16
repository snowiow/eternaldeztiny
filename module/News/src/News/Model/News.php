<?php

namespace News\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class News implements InputFilterAwareInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $accountId;

    /**
     * @var int
     */
    private $categoryId;

    /**
     * NOTE: still needed for showing of the name in the view
     * @var string
     */
    private $author;

    /**
     * NOTE: still needed for showing of the name in the view
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $date_posted;

    /**
     * @var int
     */
    private $commentCount;

    /**
     * @var InputFilter
     */
    protected $inputFilter;

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
        return $this->accountId;
    }

    /**
     * @param string $author
     */
    public function setAccountId($id)
    {
        $this->accountId = $id;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @param int
     */
    public function setCategoryId(int $categoryId)
    {
        $this->categoryId = $categoryId;
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
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getCommentCount()
    {
        return $this->commentCount;
    }

    /**
     * @return string
     */
    public function getDatePosted()
    {
        return $this->date_posted;
    }

    /**
     * Fills up the model class with the given data
     * @param array $data
     */
    public function exchangeArray($data)
    {
        $this->id           = (!empty($data['id'])) ? $data['id'] : null;
        $this->accountId    = (!empty($data['account_id'])) ? $data['account_id'] : null;
        $this->categoryId   = (!empty($data['category_id'])) ? $data['category_id'] : null;
        $this->author       = (!empty($data['name'])) ? $data['name'] : null;
        $this->category     = (!empty($data['cname'])) ? $data['cname'] : null;
        $this->title        = (!empty($data['title'])) ? $data['title'] : null;
        $this->content      = (!empty($data['content'])) ? $data['content'] : null;
        $this->commentCount = (!empty($data['comment_count'])) ? $data['comment_count'] : 0;

        $date              = new \DateTime();
        $this->date_posted = (!empty($data['date_posted'])) ? $data['date_posted'] : $date->format('Y-m-d H:i:s');
    }

    /**
     *
     * @return array NewsModel as array
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
     * Returns the InputFilter for a NewsModel.
     * @return InputFilter
     */
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
                'name'     => 'category_id',
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
                            'min'      => 1,
                            'max'      => 100,
                        ],
                    ],
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
                            'min'      => 10,
                            'max'      => 10000,
                        ],
                    ],
                ],
            ]);
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
