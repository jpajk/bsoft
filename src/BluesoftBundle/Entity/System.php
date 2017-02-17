<?php

namespace BluesoftBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * System
 *
 * @ORM\Table(name="system")
 * @ORM\Entity(repositoryClass="BluesoftBundle\Repository\SystemRepository")
 */
class System
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=500)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="support_group", type="string", length=50, unique=true)
     */
    private $supportGroup;

    /**
     * @ORM\OneToMany(targetEntity="Contract", mappedBy="system")
     */
    private $contracts;


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
     * Set name
     *
     * @param string $name
     * @return System
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return System
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set supportGroup
     *
     * @param string $supportGroup
     * @return System
     */
    public function setSupportGroup($supportGroup)
    {
        $this->supportGroup = $supportGroup;

        return $this;
    }

    /**
     * Get supportGroup
     *
     * @return string 
     */
    public function getSupportGroup()
    {
        return $this->supportGroup;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contracts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add contracts
     *
     * @param \BluesoftBundle\Entity\Contract $contracts
     * @return System
     */
    public function addContract(\BluesoftBundle\Entity\Contract $contracts)
    {
        $this->contracts[] = $contracts;

        return $this;
    }

    /**
     * Remove contracts
     *
     * @param \BluesoftBundle\Entity\Contract $contracts
     */
    public function removeContract(\BluesoftBundle\Entity\Contract $contracts)
    {
        $this->contracts->removeElement($contracts);
    }

    /**
     * Get contracts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContracts()
    {
        return $this->contracts;
    }
}
