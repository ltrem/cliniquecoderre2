<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Event
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="emergency", type="boolean")
     */
    private $emergency;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startTime", type="datetime", nullable=false)
     * @Assert\NotBlank()
     *
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endTime", type="datetime", nullable=false)
     */
    private $endTime;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="events")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="Employe", inversedBy="events")
     */
    private $employe;

    /**
     * @ORM\OneToMany(targetEntity="EventReminder", mappedBy="event")
     */
    private $eventReminders;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\EventCancellation", cascade={"persist"})
     * @ORM\JoinColumn(name="event_cancellation_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $cancellation;

    /**
     * @ORM\OneToMany(targetEntity="Receipt", mappedBy="event")
     */
    private $receipts;

    /**
     * @ORM\OneToMany(targetEntity="AppointmentAvailabilityNotification", mappedBy="eventToReplace")
     */
    private $appointmentAvailabilityNotifications;

    public function __construct() {
        $this->eventReminders = new ArrayCollection();
        $this->receipts = new ArrayCollection();
        $this->appointmentAvailabilityNotifications = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name; // shown in the breadcrumb on the create view
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
     * @return Event
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
     * @param mixed $emergency
     */
    public function setEmergency($emergency)
    {
        $this->emergency = $emergency;
    }

    /**
     * @return mixed
     */
    public function getEmergency()
    {
        return $this->emergency;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     *
     * @return Event
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     *
     * @return Event
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Note
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set employe
     *
     * @param Employe $employe
     *
     * @return Note
     */
    public function setEmploye(Employe $employe = null)
    {
        $this->employe = $employe;

        return $this;
    }

    /**
     * Get Employe
     *
     * @return \AppBundle\Entity\Employe
     */
    public function getEmploye()
    {
        return $this->employe;
    }
    
    /**
     * Get eventReminders
     *
     * @return \AppBundle\Entity\EventReminder
     */
    public function getEventReminders()
    {
        return $this->eventReminders;
    }

    /**
     * Add eventReminder
     *
     * @param \AppBundle\Entity\EventReminder $eventReminder
     *
     * @return Client
     */
    public function addEventReminder(\AppBundle\Entity\EventReminder $eventReminder)
    {
        $eventReminder->setEvent($this);
        $this->eventReminders[] = $eventReminder;

        return $this;
    }

    /**
     * Remove eventReminder
     *
     * @param \AppBundle\Entity\EventReminder $eventReminder
     */
    public function removeEventReminder(\AppBundle\Entity\EventReminder $eventReminder)
    {
        $this->eventReminders->removeElement($eventReminder);
        $eventReminder->setEvent(null);
    }

    /**
     * Set cancellation
     *
     * @param \AppBundle\Entity\EventCancellation $eventCancellation
     *
     * @return Event
     */
    public function setCancellation(EventCancellation $eventCancellation = null)
    {
        $this->cancellation = $eventCancellation;

        return $this;
    }

    /**
     * Get event
     *
     * @return \AppBundle\Entity\EventCancellation
     */
    public function getCancellation()
    {
        return $this->cancellation;
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
     * Add receipt
     *
     * @param \AppBundle\Entity\Receipt $receipt
     *
     * @return Client
     */
    public function addReceipt(\AppBundle\Entity\Receipt $receipt)
    {
        $receipt->setEvent($this);
        $this->receipts[] = $receipt;

        return $this;
    }

    /**
     * Remove receipt
     *
     * @param \AppBundle\Entity\Receipt $receipt
     */
    public function removeReceipt(\AppBundle\Entity\EventReminder $receipt)
    {
        $this->receipts->removeElement($receipt);
        $receipt->setEvent(null);
    }

    /**
     * Get appointmentAvailabilityNotification
     *
     * @return \AppBundle\Entity\AppointmentAvailabilityNotification
     */
    public function getAppointmentAvailabilityNotifications()
    {
        return $this->appointmentAvailabilityNotifications;
    }

    /**
     * Add appointmentAvailabilityNotification
     *
     * @param \AppBundle\Entity\AppointmentAvailabilityNotification $appointmentAvailabilityNotification
     *
     * @return Event
     */
    public function addAppointmentAvailabilityNotification(AppointmentAvailabilityNotification $appointmentAvailabilityNotification)
    {
        $appointmentAvailabilityNotification->setEvent($this);
        $this->appointmentAvailabilityNotifications[] = $appointmentAvailabilityNotification;

        return $this;
    }

    /**
     * Remove appointmentAvailabilityNotification
     *
     * @param \AppBundle\Entity\AppointmentAvailabilityNotification $appointmentAvailabilityNotification
     */
    public function removeAppointmentAvailabilityNotification(AppointmentAvailabilityNotification $appointmentAvailabilityNotification)
    {
        $this->appointmentAvailabilityNotifications->removeElement($appointmentAvailabilityNotification);
        $appointmentAvailabilityNotification->setEvent(null);
    }
}

