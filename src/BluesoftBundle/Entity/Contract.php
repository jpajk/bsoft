<?php

namespace BluesoftBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contract
 *
 * @ORM\Table(name="contract")
 * @ORM\Entity(repositoryClass="BluesoftBundle\Repository\ContractRepository")
 */
class Contract
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="amount_period", type="string", length=5)
     */
    private $amountPeriod;

    /**
     * @var string
     *
     * @ORM\Column(name="amount_type", type="string", length=5)
     */
    private $amountType;

    /**
     * @var float
     *
     * @ORM\Column(name="authorization_percent", type="float")
     */
    private $authorizationPercent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="from_date", type="datetime")
     */
    private $fromDate;

    /**
     * @var string
     *
     * @ORM\Column(name="order_number", type="string", length=12)
     */
    private $orderNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="request", type="string", length=12, unique=true)
     */
    private $request;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="to_date", type="datetime")
     */
    private $toDate;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Contract
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return Contract
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set amountPeriod
     *
     * @param string $amountPeriod
     * @return Contract
     */
    public function setAmountPeriod($amountPeriod)
    {
        $this->amountPeriod = $amountPeriod;

        return $this;
    }

    /**
     * Get amountPeriod
     *
     * @return string 
     */
    public function getAmountPeriod()
    {
        return $this->amountPeriod;
    }

    /**
     * Set amountType
     *
     * @param string $amountType
     * @return Contract
     */
    public function setAmountType($amountType)
    {
        $this->amountType = $amountType;

        return $this;
    }

    /**
     * Get amountType
     *
     * @return string 
     */
    public function getAmountType()
    {
        return $this->amountType;
    }

    /**
     * Set authorizationPercent
     *
     * @param float $authorizationPercent
     * @return Contract
     */
    public function setAuthorizationPercent($authorizationPercent)
    {
        $this->authorizationPercent = $authorizationPercent;

        return $this;
    }

    /**
     * Get authorizationPercent
     *
     * @return float 
     */
    public function getAuthorizationPercent()
    {
        return $this->authorizationPercent;
    }

    /**
     * Set fromDate
     *
     * @param \DateTime $fromDate
     * @return Contract
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    /**
     * Get fromDate
     *
     * @return \DateTime 
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * Set orderNumber
     *
     * @param string $orderNumber
     * @return Contract
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Get orderNumber
     *
     * @return string 
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Set request
     *
     * @param string $request
     * @return Contract
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get request
     *
     * @return string 
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set toDate
     *
     * @param \DateTime $toDate
     * @return Contract
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;

        return $this;
    }

    /**
     * Get toDate
     *
     * @return \DateTime 
     */
    public function getToDate()
    {
        return $this->toDate;
    }
}
