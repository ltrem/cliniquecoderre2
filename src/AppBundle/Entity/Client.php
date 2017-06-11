<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Client
 *
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Client
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
    private $createdAt;

    /**
     * updated Time/Date
     *
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="client", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    private $lastname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthdate", type="datetime", nullable=true)
     */
    private $birthdate;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=255, nullable=true)
     */
    private $gender;

    /**
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="client", cascade={"persist", "remove"})
     */
    private $contacts;

    /**
     * @ORM\OneToMany(targetEntity="Coordinate", mappedBy="client", cascade={"persist", "remove"})
     */
    private $coordinates;

    /**
     * @ORM\ManyToMany(targetEntity="Communication", cascade={"persist"})
     * @ORM\JoinTable(name="client_communication")
     */
    private $communications;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="client")
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity="Receipt", mappedBy="client")
     */
    private $receipts;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist"})
     * @ORM\JoinColumn(name="picture_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $picture;

    public function __construct() {
        $this->contacts = new ArrayCollection();
        $this->coordinates = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->receipts = new ArrayCollection();
        $this->communications = new ArrayCollection();
    }

    public function __toString() {
        return $this->firstname . ' ' . $this->lastname;
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Client
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Client
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Client
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    public function getFullname() {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * Set birthdate
     *
     * @param \DateTime $birthdate
     *
     * @return Client
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get birthdate
     *
     * @return \DateTime
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return Client
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }
    
    /**
     * Get contacts
     *
     * @return \AppBundle\Entity\Contact
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Add contact
     *
     * @param \AppBundle\Entity\Contact $contact
     *
     * @return Client
     */
    public function addContact(Contact $contact)
    {
        $contact->setClient($this);
        $this->contacts->add($contact);

        return $this;
    }

    /**
     * Remove contact
     *
     * @param \AppBundle\Entity\Contact $contact
     */
    public function removeContact(Contact $contact)
    {
        $this->contacts->removeElement($contact);
        $contact->setClient(null);
    }
    
    /**
     * Get coordinates
     *
     * @return \AppBundle\Entity\Coordinate
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * Add coordinate
     *
     * @param \AppBundle\Entity\Coordinate $coordinate
     *
     * @return Client
     */
    public function addCoordinate(Coordinate $coordinate)
    {
        $coordinate->setClient($this);
        $this->coordinates->add($coordinate);

        return $this;
    }

    /**
     * Remove coordinate
     *
     * @param \AppBundle\Entity\Coordinate $coordinate
     */
    public function removeCoordinate(Coordinate $coordinate)
    {
        $this->coordinates->removeElement($coordinate);
        $coordinate->setClient(null);
    }

    /**
     * Get events
     *
     * @return \AppBundle\Entity\Event
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Add event
     *
     * @param \AppBundle\Entity\Event $event
     *
     * @return Event
     */
    public function addEvent(Event $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * Get receipts
     *
     * @return \AppBundle\Entity\Receipt
     */
    public function getReceipts()
    {
        return $this->receipts;
    }

    /**
     * Add event
     *
     * @param \AppBundle\Entity\Receipt $receipt
     *
     * @return Event
     */
    public function addReceipt(Receipt $receipt)
    {
        $this->receipts[] = $receipt;

        return $this;
    }

    /**
     * Remove event
     *
     * @param \AppBundle\Entity\Event $event
     */
    public function removeEvent(Event $event)
    {
        $this->events->removeElement($event);
    }

    /**
     * Get communications
     *
     * @return \AppBundle\Entity\Communication
     */
    public function getCommunications()
    {
        return $this->communications;
    }

    /**
     * Add communication
     *
     * @param \AppBundle\Entity\Communication $communication
     */
    public function addCommunication(Communication $communication)
    {
        $this->communications[] = $communication;
    }

    /**
     * Remove communication
     *
     * @param \AppBundle\Entity\Communication $communication
     */
    public function removeCommunication(Communication $communication)
    {
        $this->communications->removeElement($communication);
    }

    /**
     * @return mixed
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param mixed $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }
}

