<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CellCarrier
 *
 * @ORM\Table(name="cell_carrier")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CellCarrierRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CellCarrier
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
     * created Time/Date
     *
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * updated Time/Date
     *
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    protected $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="mailToSMS", type="string", length=255)
     */
    private $mailToSMS;

    /**
     * @var string
     *
     * @ORM\Column(name="available", type="string", length=255)
     */
    private $available;


    public function __toString() {
        return $this->name;
    }
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @ORM\PreUpdate
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return CellCarrier
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
     * Set mailToSMS
     *
     * @param string $mailToSMS
     *
     * @return CellCarrier
     */
    public function setMailToSMS($mailToSMS)
    {
        $this->mailToSMS = $mailToSMS;

        return $this;
    }

    /**
     * Get mailToSMS
     *
     * @return string
     */
    public function getMailToSMS()
    {
        return $this->mailToSMS;
    }

    /**
     * Set available
     *
     * @param string $available
     *
     * @return CellCarrier
     */
    public function setAvailable($available)
    {
        $this->available = $available;

        return $this;
    }

    /**
     * Get available
     *
     * @return string
     */
    public function getAvailable()
    {
        return $this->available;
    }
}

