<?php

namespace ApplyNow\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

interface Status
{
    const NOT_PROCESSED = 0;
    const ACCEPTED      = 1;
    const DECLINED      = 1 << 1;

}

function statusToString(\int $status)
{
    switch ($status) {
        case (Status::ACCEPTED):
            return 'accepted';
        case (Status::DECLINED):
            return 'declined';
        default:
            return 'not processed';
    }
}

class Application implements InputFilterAwareInterface
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
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $tag;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $strategies;

    /**
     * @var int
     */
    private $th;

    /**
     * @var int
     */
    private $warStars;

    /**
     * @var int
     */
    private $age;

    /**
     * @var string
     */
    private $infos;

    /**
     * @var string
     */
    private $why;

    /**
     * @var string
     */
    private $basebic;

    /**
     * @var string
     */
    private $profilebic;

    /**
     * @var bool
     */
    private $processed;

    /**
     * @var int
     */
    private $processedBy;

    /**
     * @var string
     */
    private $account_name;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

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
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getStrategies()
    {
        return $this->strategies;
    }

    /**
     * @param string $strats
     */
    public function setStrategies($strats)
    {
        $this->strategies = $strats;
    }

    /**
     * @return int
     */
    public function getTh()
    {
        return $this->th;
    }

    /**
     * @param int $th
     */
    public function setTh($th)
    {
        $this->th = $th;
    }

    /**
     * @return int
     */
    public function getWarStars()
    {
        return $this->warStars;
    }

    /**
     * @param int $warStars
     */
    public function setWarStars($warStars)
    {
        $this->warStars = $warStars;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return string
     */
    public function getInfos()
    {
        return $this->infos;
    }

    /**
     * @param string $infos
     */
    public function setInfos($infos)
    {
        $this->infos = $infos;
    }

    /**
     * @return string
     */
    public function getWhy()
    {
        return $this->why;
    }

    /**
     * @param string $why
     */
    public function setWhy($why)
    {
        $this->why = $why;
    }

    /**
     * @return string
     */
    public function getBasepic()
    {
        return $this->basepic;
    }

    /**
     * @param string $basePic
     */
    public function setBasepic($basepic)
    {
        $this->basepic = $basepic;
    }

    /**
     * @return string
     */
    public function getProfilepic()
    {
        return $this->profilepic;
    }

    /**
     * @param string $profilePic
     */
    public function setProfilepic($profilepic)
    {
        $this->profilepic = $profilepic;
    }

    /**
     * @return bool
     */
    public function getProcessed()
    {
        return $this->processed;
    }

    /**
     * @param bool $processed
     */
    public function setProcessed($processed)
    {
        $this->processed = $processed;
    }

    /**
     * @param int $processedBy
     */
    public function setProcessedBy($id)
    {
        $this->processedBy = $id;
    }

    /**
     * @return int
     */
    public function getProcessedBy()
    {
        return $this->processedBy;
    }

    public function getAccountName()
    {
        return $this->account_name;
    }
    /**
     * Fills up the model class with the given data
     * @param array $data The account data needed to fill the model
     */
    public function exchangeArray($data)
    {
        $this->id           = (!empty($data['id'])) ? $data['id'] : null;
        $this->name         = !empty($data['name']) ? $data['name'] : null;
        $this->tag          = !empty($data['tag']) ? $data['tag'] : null;
        $this->email        = !empty($data['email']) ? $data['email'] : null;
        $this->strategies   = !empty($data['strategies']) ? $data['strategies'] : null;
        $this->th           = !empty($data['th']) ? $data['th'] : null;
        $this->warStars     = !empty($data['warStars']) ? $data['warStars'] : null;
        $this->age          = !empty($data['age']) ? $data['age'] : null;
        $this->infos        = !empty($data['infos']) ? $data['infos'] : null;
        $this->why          = !empty($data['why']) ? $data['why'] : null;
        $this->basepic      = !empty($data['basepic']) ? $data['basepic'] : null;
        $this->profilepic   = !empty($data['profilepic']) ? $data['profilepic'] : null;
        $this->processed    = !empty($data['processed']) ? $data['processed'] : 0;
        $this->processedBy  = !empty($data['processed_by']) ? $data['processed_by'] : null;
        $this->account_name = !empty($data['account_name']) ? $data['account_name'] : null;
    }

    /**
     * @return array AccountModel as array
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
     * Creates the InputFilter for the ApplyNow form.
     * @return \Zend\InputFilter\InputFilter
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name'       => 'name',
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
                            'max'      => 64,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'       => 'tag',
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
                            'max'      => 64,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'       => 'strategies',
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'       => 'email',
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
                            'min'      => 4,
                            'max'      => 255,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'       => 'info',
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'       => 'why',
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'th',
                'required' => true,
            ]);

            $inputFilter->add([
                'name'       => 'warStars',
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
                            'max'      => 4,
                        ],
                    ],
                ],

            ]);

            $inputFilter->add([
                'name'       => 'age',
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
                            'max'      => 2,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'processed',
                'required' => false,
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
