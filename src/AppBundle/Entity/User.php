<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="It looks like your already have an account!")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Client", mappedBy="user", cascade={"persist"})
     */
    private $client;

    /**
     * @ORM\OneToOne(targetEntity="Employe", mappedBy="user", cascade={"persist"})
     */
    private $employe;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * The encoded password
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * A non-persisted field that's used to create the encoded password.
     * @Assert\NotBlank(groups={"Registration"})
     *
     * @var string
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /**
     * @var string
     *
     * @ORM\Column(name="reset_password_token", type="string", length=255, nullable=true)
     */
    private $resetPasswordToken;

    /**
     * @var string
     *
     * @ORM\Column(name="reset_password_date", type="datetime", nullable=true)
     */
    private $resetPasswordDate;


    public function __toString() {
        return $this->getUsername();
    }

    // Get Client associated to User
    public function getClient()
    {
        return $this->client;
    }
    // Set Client to user
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function getEmploye()
    {
        return $this->employe;
    }

    /**
     * @param mixed $employe
     */
    public function setEmploye(Employe $employe)
    {
        $this->employe = $employe;
    }

    // needed by the security system

    public function getUsername()
    {
        return $this->email;
    }
    public function getRoles()
    {
        $roles = $this->roles;

        // give everyone ROLE_USER!
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        return $roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        // leaving blank - I don't need/have a password!
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        // forces the object to look "dirty" to Doctrine. Avoids
        // Doctrine *not* saving this entity, if only plainPassword changes
        $this->password = null;
    }

    /**
     * @return string
     */
    public function getResetPasswordToken()
    {
        return $this->resetPasswordToken;
    }


    /**
     * @param string $resetPasswordToken
     */
    public function setResetPasswordToken($resetPasswordToken)
    {
        $this->resetPasswordToken = $resetPasswordToken;
    }

    /**
     * @return string
     */
    public function getResetPasswordDate()
    {
        return $this->resetPasswordDate;
    }

    /**
     * @param string $resetPasswordDate
     */
    public function setResetPasswordDate($resetPasswordDate)
    {
        $this->resetPasswordDate = $resetPasswordDate;
    }


    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }
}
