<?php

namespace Warstatus\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;

class Warstatus implements InputFilterAwareInterface
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $optedOutDate;

    /**
     * @var string
     */
    private $optedInDate;

    /**
     * @var string
     */
    private $reason;

    /**
     * @var bool
     */
    private $gemable;

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
    public function getOptedOutDate()
    {
        return $this->optedOutDate;
    }

    /**
     * @param string $date
     */
    public function setOptedOutDate(string $date)
    {
        $this->optedOutDate = $date;
    }

    /**
     * @return string
     */
    public function getOptedInDate()
    {
        return $this->optedInDate;
    }

    /**
     * @param string $date
     */
    public function setOptedInDate(string $date)
    {
        $this->optedInDate = $date;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     */
    public function setReason(string $reason)
    {
        $this->reason = $reason;
    }

    /**
     * @return bool
     */
    public function getGemable()
    {
        return $this->gemable;
    }

    /**
     * @param bool $gemable
     */
    public function setGemable(bool $gemable)
    {
        $this->gemable = $gemable;
    }

    /**
     * Fills up the model class with the given data
     * @param array $data
     */
    public function exchangeArray(array $data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;

        $dateNow = new \DateTime();
        $dateNow = $dateNow->format('Y-m-d H:i:s');

        $this->optedInDate  = (!empty($data['opted_in_date'])) ? $data['opted_in_date'] : $dateNow;
        $this->optedOutDate = (!empty($data['opted_out_date'])) ? $data['opted_out_date'] : $dateNow;

        $this->reason = (!empty($data['reason'])) ? $data['reason'] : null;
        $this->gemable(!empty($data['gemable'])) ? $data['gemable'] : 0;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFIlter)
    {
        throw new \Exception('Not used');
    }

    /**
     * Creates the InputFilter for a Warstats Model
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name'       => 'reason',
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
                            'max'      => 255,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'opted_out_date',
                'required' => true,
            ]);

            $inputFilter->add([
                'name'     => 'opted_in_date',
                'required' => false,
            ]);

            $inputFilter->add([
                'name'     => 'gemable',
                'required' => true,
            ]);
        }

        return $this->inputFilter;
    }
}
