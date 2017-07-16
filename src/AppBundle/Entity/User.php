<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Emp\UserBundle\Entity\Assert;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation\AccessorOrder;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 *
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 * @ExclusionPolicy("all")
 * @AccessorOrder("custom", custom = {"id", "username", "email", "groups"})
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Expose
     * @Type("string")
     * @Groups({"users_all","users_summary"})
     */
    protected $id;


    /**
     * @Expose
     * @Type("string")
     * @Groups({"users_all","users_summary"})
     */
    protected $username;

    /**
     * @Expose
     * @Type("string")
     * @Groups({"users_all","users_summary"})
     */
    protected $email;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Group",  inversedBy="users")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     * @Expose
     * 
     */
    protected $groups;
    
     /**
     * @ORM\Column(type="string", length=255)
     * @Expose
     * @Type("string")
     * @Groups({"Registration", "Profile", "users_all","users_summary"})
     */
    protected $name;


    public function __construct()
    {
        parent::__construct();
        $this->groups = new ArrayCollection();
        // your own logic
    }

     /**
     * Set name
     *
     * @param string $firstName
     * @return User
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

}
