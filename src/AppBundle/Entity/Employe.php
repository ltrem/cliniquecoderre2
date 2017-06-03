<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Employe
 *
 * @ORM\Table(name="employe")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmployeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Employe
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
     * @ORM\OneToOne(targetEntity="User", inversedBy="employe", cascade={"persist"})
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
     * @ORM\Column(name="birthdate", type="datetime", nullable=false)
     */
    private $birthdate;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=255)
     */
    private $gender;

    /**
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="employe", cascade={"persist", "remove"}))
     */
    private $contacts;

    /**
     * @ORM\OneToMany(targetEntity="Coordinate", mappedBy="employe", cascade={"persist", "remove"})
     */
    private $coordinates;

    /**
     * @ORM\ManyToMany(targetEntity="Communication", cascade={"persist"})
     * @ORM\JoinTable(name="employe_communication")
     */
    private $communications;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="employe")
     */
    private $events;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist"})
     * @ORM\JoinColumn(name="picture_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $picture;

    /**
     * @ORM\OneToMany(targetEntity="Schedule", mappedBy="employe")
     * @ORM\JoinColumn(name="picture_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $schedules;

    public function __construct() {
        $this->contacts = new ArrayCollection();
        $this->coordinates = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->communications = new ArrayCollection();
        $this->schedules = new ArrayCollection();
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
     * @return Employe
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
     * @return Employe
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
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Employe
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFullname() {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * Set birthdate
     *
     * @param \DateTime $birthdate
     *
     * @return Employe
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
     * @return Employe
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
     * @return Employe
     */
    public function addContact(Contact $contact)
    {
        $contact->setEmploye($this);
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
        $contact->setEmploye(null);
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
     * @return Employe
     */
    public function addCoordinate(Coordinate $coordinate)
    {
        $coordinate->setEmploye($this);
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
        $coordinate->setEmploye(null);
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
     * Remove event
     *
     * @param \AppBundle\Entity\Event $event
     */
    public function removeEvent(Event $event)
    {
        $this->events->removeElement($event);
    }

    /**
     * Get schedules
     *
     */
    public function getSchedules()
    {
        return $this->schedules;
    }

    /**
     * Get schedules blocks
     */
    public function getScheduleBlocks()
    {
        return $this->schedules;
    }

    /**
     * Add schedule
     *
     * @param \AppBundle\Entity\ScheduleBlock $schedule
     *
     * @return Client
     */
    public function addScheduleBlock(\AppBundle\Entity\ScheduleBlock $schedule)
    {
        $this->schedules[] = $schedule;

        return $this;
    }

    /**
     * Remove schedule
     *
     * @param \AppBundle\Entity\ScheduleBlock $schedule
     */
    public function removeScheduleBlock(\AppBundle\Entity\ScheduleBlock $schedule)
    {
        $this->events->removeElement($schedule);
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

