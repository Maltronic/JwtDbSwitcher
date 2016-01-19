<?php

namespace Maltronic\Bundle\JwtDbSwitcher\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Database
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Database
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $displayName;

    /**
     * @ORM\ManyToMany(targetEntity="AuthUser", mappedBy="databases")
     **/
    private $authUsers;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Parameters", mappedBy="database")
     */
    private $parameters;

    public function __construct()
    {
        $this->authUsers = new ArrayCollection();
    }

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
     *
     * @return Database
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
     * Set displayName
     *
     * @param string $displayName
     *
     * @return Database
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Get AuthUsers
     *
     * @return ArrayCollection
     */
    public function getAuthUsers()
    {
        return $this->authUsers;
    }

    /**
     * Add authUser
     *
     * @param AuthUser $authUser
     *
     * @return Database
     */
    public function addAuthUser(AuthUser $authUser)
    {
        $this->authUsers[] = $authUser;

        return $this;
    }

    /**
     * Remove authUser
     *
     * @param AuthUser $authUser
     */
    public function removeAuthUser(AuthUser $authUser)
    {
        $this->authUsers->removeElement($authUser);
    }

    /**
     * Add Auth Parameters
     *
     * @param Parameter $parameter
     *
     * @return Database
     */
    public function addParameter(Parameter $parameter)
    {
        $this->parameters[] = $parameter;

        return $this;
    }

    /**
     * Remove Parameter
     *
     * @param Parameter $parameter
     */
    public function removeParameter(Parameter $parameter)
    {
        $this->parameters->removeElement($parameter);
    }

    /**
     * Get Parameters
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
