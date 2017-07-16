<?php

namespace AppBundle\Controller;

use AppBundle\DTO\UserGroupsDTO;
use AppBundle\Entity\User;
use AppBundle\Form\Type\GroupUserType;
use AppBundle\Handler\UserHandler;
use Codeception\Lib\Console\ucfirst;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
* Class RestUserController
* @RouteResource("user", pluralize=true)
*/
class RestUserController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Delete a user given the id
     * @param  $userId the user id 
     */
    public function deleteAction($userId)
    {
        $requestedUser = $this->get('crv.repository.restricted_user_repository')->findOneById($userId);
        $deletedUser = $this->getUserHandler()->delete($requestedUser);
        $view = new View(null, Response::HTTP_NO_CONTENT);

        return $view;
    }


     /**
     * Remove users to group
     * @param  integer $userId    user id
     * @param  string $groupName  group name
     * @return UserInterface $user
     * @Post("/users/removegroup")
     */
    public function postRemoveUsersToGroupAction(Request $request)
    {
        $action = array('addGroup' => false);
        $action['msg'] = 'remove user from group successfully';
        $response = $this->changeUsersGroups($request->request->all(), $action);

        return $response;
    }
    /**
     * Associate group and users
     * @param  integer $userId    user id
     * @param  string $groupName  group name
     * @return UserInterface $user
     * @Post("/users/addgroup")
     */
    public function postAddUsersToGroupsAction(Request $request)
    {
        $action = array('addGroup' => true);
        $action['msg'] = 'users assign to a group successfully';
        $response = $this->changeUsersGroups($request->request->all(), $action);

        return $response;
    }

    private function changeUsersGroups($parameters, $actions, array $options = [])
    {
        $userGroupsDTO = $this->getUserGroupHandler()->handle(
            new UserGroupsDTO,
            $parameters,
            Request::METHOD_POST,
            $options
        );
        $group = $this->findGroupBy('name', $userGroupsDTO->getName());
        $user = $this->getUserHandler()->postChangeUsersGroups($userGroupsDTO->getUserId(), $group, $actions);

        $response = new JsonResponse(
            [
                'msg' => $actions['msg'],
            ],
            JsonResponse::HTTP_OK,
            [
                'Location' => $this->generateUrl(
                    'get_profile',
                    [ 'user' => $user->getId() ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            ]
        );

        return $response;
    }


     /**
     * Find a group by a specific property.
     *
     * @param string $key   property name
     * @param mixed  $value property value
     *
     * @throws NotFoundHttpException if user does not exist
     *
     * @return GroupInterface
     */
    protected function findGroupBy($key, $value)
    {
        if (!empty($value)) {
            $group = $this->get('fos_user.group_manager')->{'findGroupBy'.ucfirst($key)}($value);
        }

        if (empty($group)) {
            throw new \DomainException(sprintf('The group with "%s" does not exist for value "%s"', $key, $value));
        }

        return $group;
    }

    /**
     * Get a single User.
     *
     * @ApiDoc(
     *   output = "AppBundle\Entity\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when not found"
     *   }
     * )
     *
     * @param int   $userId     the user id
     *
     * @throws NotFoundHttpException when does not exist
     *
     * @return View
     */  
    public function getAction($userId)
    {
        $user = $this->getUserHandler()->get($userId);
        $view = $this->view($user);
        return $view;
    }

    /**
     * @return UserHandler
     */
    private function getUserHandler()
    {
        return $this->container->get('crv.handler.restricted_user_handler');
    }

    private function getUserGroupHandler()
    {
        return $this->container->get('crv.form.handler.user_group_form_handler');
    }

    /**
     * Gets a collection of Users.
     *
     * @ApiDoc(
     *   output = "AppBundle\Entity\User",
     *   statusCodes = {
     *     405 = "Method not allowed"
     *   }
     * )
     *
     * @throws MethodNotAllowedHttpException
     *
     * @return View
     */
    public function cgetAction()
    {
        throw new MethodNotAllowedHttpException([], "Method not allowed");
    }    
}
