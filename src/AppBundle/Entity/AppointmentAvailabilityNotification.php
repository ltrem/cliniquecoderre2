<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AppointmentAvailabilityNotification
 *
 * @ORM\Table(name="appointment_availability_notification")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AppointmentAvailabilityNotificationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class AppointmentAvailabilityNotification
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
     * Many AppointmentAvailabiityNotification has one Event Freed
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(name="event_id_freed", referencedColumnName="id")
     */
    private $eventFreed;

    /**
     * One AppointmentAvailabiityNotification has one Event to replace with.
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="appointmentAvailabilityNotifications")
     * @ORM\JoinColumn(name="event_id_to_replace", referencedColumnName="id")
     */
    private $eventToReplace;

    /**
     * One AppointmentAvailabiityNotification has One Communication.
     * @ORM\OneToOne(targetEntity="Communication", cascade={"persist"})
     * @ORM\JoinColumn(name="communication_id", referencedColumnName="id")
     */
    private $communication;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * @var int
     *
     * @ORM\Column(name="answer", type="integer", nullable=true)
     */
    private $answer;


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
     * Set client
     *
     * @param string $client
     *
     * @return AppointmentAvailabilityNotification
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return string
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set eventFreed
     *
     * @param string $eventFreed
     *
     * @return Event
     */
    public function setEventFreed($eventFreed)
    {
        $this->eventFreed = $eventFreed;

        return $this;
    }

    /**
     * Get eventFreed
     *
     * @return string
     */
    public function getEventFreed()
    {
        return $this->eventFreed;
    }

    /**
     * Set eventToReplace
     *
     * @param string $eventToReplace
     *
     * @return Event
     */
    public function setEventToReplace($eventToReplace)
    {
        $this->eventToReplace = $eventToReplace;

        return $this;
    }

    /**
     * Get eventToReplace
     *
     * @return string
     */
    public function getEventToReplace()
    {
        return $this->eventToReplace;
    }

    /**
     * Set communication
     *
     * @param string $communication
     *
     * @return AppointmentAvailabilityNotification
     */
    public function setCommunication($communication)
    {
        $this->communication = $communication;

        return $this;
    }

    /**
     * Get communication
     *
     * @return string
     */
    public function getCommunication()
    {
        return $this->communication;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return AppointmentAvailabilityNotification
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set answer
     *
     * @param integer $answer
     *
     * @return AppointmentAvailabilityNotification
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return int
     */
    public function getAnswer()
    {
        return $this->answer;
    }
}

