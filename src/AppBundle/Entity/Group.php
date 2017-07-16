<?php
// src/AppBundle/Entity/Group.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\Group as BaseGroup;
use JMS\Serializer\Annotation\AccessorOrder;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_group")
 * @ExclusionPolicy("all")
 * @AccessorOrder("custom", custom = {"id", "name", "roles"})
 */
class Group extends BaseGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
     protected $id;

     /**
      * @Expose
      * @Type("string")
      * @Groups({"users_all","users_summary"})
      */
     protected $name;

     /**
      * @Expose
      * @Groups({"users_all","users_summary"})
      */
     protected $roles;

     /**
    * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="groups")
    * @Expose
    * @Groups({"users_all","users_summary"})
    */
     protected $users;

    /**
     * Add user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Group
     */
    public function addUser(\AppBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeUser(\AppBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
