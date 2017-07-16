<?php

namespace AppBundle\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class UserGroupsDTO implements SymfonyFormDTO
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var number
     * @Assert\NotBlank()
     */
    private $userId;

//    /**
//     * @var array
//     */
//    private $files;

    public function __construct()
    {
//        $this->files = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getDataClass()
    {
        return self::class;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'name'                  => $this->name,
            'userId'                 => $this->userId,
//            'files'                 => $this->files,
        ];
    }

    /**
     * @return userId
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

}
