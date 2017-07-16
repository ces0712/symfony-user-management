<?php

namespace AppBundle\Handler;
use AppBundle\Form\Handler\FormHandlerInterface;
use AppBundle\Repository\UserRepositoryInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
class UserHandler implements HandlerInterface
{
    /**
     * @var FormHandlerInterface
     */
    private $formHandler;
    /**
     * @var UserRepositoryInterface
     */
    private $repository;
    public function __construct(
        FormHandlerInterface $formHandler,
        UserRepositoryInterface $userRepositoryInterface
    )
    {
        $this->formHandler = $formHandler;
        $this->repository = $userRepositoryInterface;
    }
    public function get($id)
    {
        return $this->repository->findOneById($id);
    }
    public function all($limit = 10, $offset = 0)
    {
        throw new \DomainException('UserHandler::all is currently not implemented.');
    }
    public function post(array $parameters, array $options = [])
    {
        throw new \DomainException('UserHandler::post is currently not implemented.');
    }
    public function put($resource, array $parameters, array $options = [])
    {
        throw new \DomainException('UserHandler::put is currently not implemented.');
    }
    /**
     * @param UserInterface     $user
     * @param array             $parameters
     * @param array             $options
     * @return UserInterface
     */
    public function patch($user, array $parameters, array $options = [])
    {
        $this->guardUserImplementsInterface($user);
        $user = $this->formHandler->handle(
            $user,
            $parameters,
            Request::METHOD_PATCH,
            $options
        );
        $this->repository->save($user);
        return $user;
    }

    public function delete($resource)
    {
        $this->guardUserImplementsInterface($resource);
        $this->repository->delete($resource);
        return true;
    }

    /**
     * @param  Integer $userId  id user
     * @param  GroupInterface $group   
     * @param  array  $actions 
     * @return UserInterface
     */
    public function postChangeUsersGroups($userId, $group, array $actions = [])
    {
        $user = $this->repository->findOneById($userId);
        $this->guardUserImplementsInterface($user);
        if ($actions['addGroup'] === true) {
            $user->addGroup($group);
        }else if ($actions['addGroup'] === false) {
            $user->removeGroup($group);
        }
        $this->repository->save($user);
        return $user;
    }

    private function guardUserImplementsInterface($user)
    {
        if (!$user instanceof UserInterface) {
            throw new \InvalidArgumentException('Expected passed User to implement UserInterface');
        }
    }

}