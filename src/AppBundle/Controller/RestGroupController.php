<?php

namespace AppBundle\Controller;

use Codeception\Lib\Console\ucfirst;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\UserBundle\Event\FilterGroupResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GroupEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\GroupManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
* Class RestGroupController
* @RouteResource("group", pluralize=false)
*/
class RestGroupController extends FOSRestController implements ClassResourceInterface
{
    
    public function postAction(Request $request)
    {
        /** @var $groupManager GroupManagerInterface */
        $groupManager = $this->get('fos_user.group_manager');
        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.group.form.factory');
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $group = $groupManager->createGroup('');


        $dispatcher->dispatch(FOSUserEvents::GROUP_CREATE_INITIALIZE, new GroupEvent($group, $request));

        $form = $formFactory->createForm(['csrf_protection' => false]);
        $form->setData($group);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }
        
        $event = new FormEvent($form, $request);
        $dispatcher->dispatch(FOSUserEvents::GROUP_CREATE_SUCCESS, $event);

        $groupManager->updateGroup($group);

        if (null === $response = $event->getResponse()) {
            return new JsonResponse($this->get('translator')->trans('group.flash.created', [], 'FOSUserBundle'),  Response::HTTP_CREATED);
        }

        $dispatcher->dispatch(FOSUserEvents::GROUP_CREATE_COMPLETED, new FilterGroupResponseEvent($group, $request, $response));
            return new JsonResponse($this->get('translator')->trans('group.flash.created', [], 'FOSUserBundle'),  Response::HTTP_CREATED);
    }

    /**
     * @Get("/group/list")
     */
    public function listAction()
    {
        $groups = $this->get('fos_user.group_manager')->findGroups();
        return $groups;
    }

    /**
     * [deleteAction description]
     * @param  Request $request   
     * @param  String  $groupName
     * @Delete("/group/{groupName}/delete") 
     */
    public function deleteAction(Request $request, $groupName)
    {
        $group = $this->findGroupBy('name', $groupName);

        if (count($group->getUsers()) === 0) {
            $this->get('fos_user.group_manager')->deleteGroup($group);            
            $response = new JsonResponse($this->get('translator')->trans('group.flash.deleted', [], 'FOSUserBundle'),Response::HTTP_OK);
            /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
            $dispatcher = $this->get('event_dispatcher');

            $dispatcher->dispatch(FOSUserEvents::GROUP_DELETE_COMPLETED, new FilterGroupResponseEvent($group, $request, $response));
        }else {
            $response = new JsonResponse('error this group has a member',Response::HTTP_BAD_REQUEST);
        }

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

}
