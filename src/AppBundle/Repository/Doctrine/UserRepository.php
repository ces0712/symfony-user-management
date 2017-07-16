<?php

// src/AppBundle/Repository/Doctrine/UserRepository.php

namespace AppBundle\Repository\Doctrine;

use AppBundle\Repository\Doctrine\CommonDoctrineRepository;
use AppBundle\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserInterface;
// use AppBundle\Model\UserInterface;

class UserRepository implements UserRepositoryInterface
{

    /**
     * @var CommonDoctrineRepository
     */
    private $commonRepository;

    private $em;

    /**
     * DoctrineUserRepository constructor.
     * @param CommonDoctrineRepository $commonRepository
     * @param EntityManagerInterface $em
     */

     public function __construct(CommonDoctrineRepository $commonRepository, EntityManagerInterface $em)
    {
        $this->commonRepository = $commonRepository;
        $this->em = $em;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findOneById($id)
    {
        return $this->em->getRepository('AppBundle:User')->find($id);
    }



    /**
     * @param UserInterface         $user
     * @param array                 $arguments
     */
    public function save(UserInterface $user, array $arguments = ['flush'=>true])
    {
        $this->commonRepository->save($user, $arguments);
    }

    /**
     * @param UserInterface         $user
     * @param array                 $arguments
     */
    public function delete(UserInterface $user, array $arguments = ['flush'=>true])
    {
        $this->commonRepository->delete($user, $arguments);
    }


}