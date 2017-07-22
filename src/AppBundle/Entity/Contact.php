<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumberUtil;

/**
 * Contact
 *
 * @ORM\Table(name="contact")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContactRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Contact
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
     * @ORM\Column(name="phone_cell", type="phone_number", nullable=false)
     */
    private $phoneCell;

    /**
     *
     * @ORM\ManyToOne(targetEntity="CellCarrier")
     */
    private $phoneCellCarrier;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_work", type="phone_number", nullable=true)
     */
    private $phoneWork;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_home", type="phone_number", nullable=true)
     */
    private $phoneHome;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="contacts")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="Employe", inversedBy="contacts")
     */
    private $employe;

    public function __toString() {
        return (string) $this->getPhoneCell();
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
     * Set phoneCell
     *
     * @param string $phoneCell
     *
     * @return Contact
     */
    public function setPhoneCell($phoneCell)
    {
        $this->phoneCell = $phoneCell;

        return $this;
    }

    /**
     * Get phoneCell
     *
     * @return string
     */
    public function getPhoneCell()
    {
        return $this->phoneCell;
    }

    /**
     * Set phoneCellCarrier
     *
     * @param CellCarrier $phoneCellCarrier
     *
     * @return Contact
     */
    public function setPhoneCellCarrier(CellCarrier $phoneCellCarrier)
    {
        $this->phoneCellCarrier = $phoneCellCarrier;

        return $this;
    }

    /**
     * Get phoneCellCarrier
     *
     * @return CellCarrier PhoneCellCarrier
     */
    public function getPhoneCellCarrier()
    {
        return $this->phoneCellCarrier;
    }

    /**
     * Set phoneWork
     *
     * @param string $phoneWork
     *
     * @return Contact
     */
    public function setPhoneWork($phoneWork)
    {
        $this->phoneWork = $phoneWork;

        return $this;
    }

    /**
     * Get phoneWork
     *
     * @return string
     */
    public function getPhoneWork()
    {
        return $this->phoneWork;
    }

    /**
     * Set phoneHome
     *
     * @param string $phoneHome
     *
     * @return Contact
     */
    public function setPhoneHome($phoneHome)
    {
        $this->phoneHome = $phoneHome;

        return $this;
    }

    /**
     * Get phoneHome
     *
     * @return string
     */
    public function getPhoneHome()
    {
        return $this->phoneHome;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Communication
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Employe
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set employe
     *
     * @param \AppBundle\Entity\Employe $employe
     *
     * @return Communication
     */
    public function setEmploye(\AppBundle\Entity\Employe $employe = null)
    {
        $this->employe = $employe;

        return $this;
    }

    /**
     * Get employe
     *
     * @return \AppBundle\Entity\Employe
     */
    public function getEmploye()
    {
        return $this->employe;
    }
}

