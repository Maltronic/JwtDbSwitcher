<?php

namespace Maltronic\Bundle\JwtDbSwitcher\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AuthUserRepository")
 */
class AuthUser implements \Serializable, UserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Database", inversedBy="authUsers", cascade={"persist"})
     * @ORM\JoinTable(name="AuthUsers_Databases")
     **/
    private $databases;

    /**
     * @ORM\Column(name="last_login", type="datetime")
     */
    private $lastLoggedIn;

    /**
     * @ORM\Column(name="last_token", type="string")
     */
    private $lastToken;

    /**
     * @ORM\Column(name="password_reset_required", type="boolean")
     */
    private $passwordResetRequired;

    public function __construct()
    {
        $this->isActive = true;
        $this->sites    = new ArrayCollection();
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return array('ROLE_USER', 'ROLE_API');
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    /**
     * Get Databases
     *
     * @return ArrayCollection
     */
    public function getDatabases()
    {
        return $this->databases;
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
     * Set username
     *
     * @param string $username
     *
     * @return AuthUser
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return AuthUser
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return AuthUser
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set lastLoggedIn
     *
     * @param \DateTime $lastLoggedIn
     *
     * @return AuthUser
     */
    public function setLastLoggedIn($lastLoggedIn)
    {
        $this->lastLoggedIn = $lastLoggedIn;

        return $this;
    }

    /**
     * Get lastLoggedIn
     *
     * @return \DateTime
     */
    public function getLastLoggedIn()
    {
        return $this->lastLoggedIn;
    }

    /**
     * Add Database
     *
     * @param Database $database
     *
     * @return AuthUser
     */
    public function addDatabase(Database $database)
    {
        $this->databases[] = $database;

        return $this;
    }

    /**
     * Remove globalSite
     *
     * @param Database $database
     */
    public function removeDatabase(Database $database)
    {
        $this->databases->removeElement($database);
    }

    /**
     * Set lastToken
     *
     * @param string $lastToken
     *
     * @return AuthUser
     */
    public function setLastToken($lastToken)
    {
        $this->lastToken = $lastToken;

        return $this;
    }

    /**
     * Get lastToken
     *
     * @return string
     */
    public function getLastToken()
    {
        return $this->lastToken;
    }

    /**
     * Set passwordResetRequired
     *
     * @param boolean $passwordResetRequired
     *
     * @return AuthUser
     */
    public function setPasswordResetRequired($passwordResetRequired)
    {
        $this->passwordResetRequired = $passwordResetRequired;

        return $this;
    }

    /**
     * Get passwordResetRequired
     *
     * @return boolean
     */
    public function getPasswordResetRequired()
    {
        return $this->passwordResetRequired;
    }
}
