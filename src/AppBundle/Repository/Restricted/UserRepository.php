<?php

// src/AppBundle/Repository/Restricted/UserRepository.php

namespace AppBundle\Repository\Restricted;

// use AppBundle\Model\UserInterface;
use AppBundle\Repository\Doctrine\UserRepository as DoctrineUserRepository;
use AppBundle\Repository\RepositoryInterface;
use AppBundle\Repository\UserRepositoryInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserRepository implements UserRepositoryInterface, RepositoryInterface
{
    /**
     * @var DoctrineUserRepository
     */
    private $userRepository;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * UserRepository constructor.
     * @param DoctrineUserRepository        $userRepository
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        DoctrineUserRepository $userRepository,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->userRepository = $userRepository;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        $user = $this->userRepository->find($id);
        $this->denyAccessUnlessGranted('view', $user);

        return $user;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findOneById($id)
    {
        $user = $this->userRepository->findOneById($id);
        $this->denyAccessUnlessGranted('view', $user);

        return $user;
    }



    /**
     * @param        $attribute
     * @param null   $object
     * @param string $message
     */
    protected function denyAccessUnlessGranted($attribute, $object = null, $message = 'Access Denied')
    {
        
        if ( ! $this->authorizationChecker->isGranted($attribute, $object)) {
            throw new AccessDeniedHttpException($message);
        }
    }


    /**
     * @param UserInterface         $user
     * @param array                 $arguments
     */
    public function save(UserInterface $user, array $arguments = ['flush'=>true])
    {
        $this->authorizationChecker->isGranted('view', $user);
        $this->userRepository->save($user);
    }

    /**
     * @param UserInterface         $user
     * @param array                 $arguments
     */
    public function delete(UserInterface $user, array $arguments = ['flush'=>true])
    {
        $this->authorizationChecker->isGranted('view', $user);
        $this->userRepository->delete($user);
    }


}
