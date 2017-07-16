<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
* Class RestLoginController
* @RouteResource("profile", pluralize=false)
*/
class RestProfileController extends FOSRestController implements ClassResourceInterface
{

  /**
     * @param Request       $request
     * @param UserInterface $user
     *
     * @ParamConverter("user", class="AppBundle:User")
     *
     * @return FormInterface
     */
    public function putAction(Request $request, UserInterface $user)
    {
        $user = $this->getAction($user);

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm(['csrf_protection' => false]);
        $form->setData($user);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $event = new FormEvent($form, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

        $userManager->updateUser($user);

        // there was no override
        if (null === $response = $event->getResponse()) {
            return $this->routeRedirectView(
                'get_profile',
                ['user' => $user->getId()],
                Response::HTTP_NO_CONTENT
            );
        }

        // unsure if this is now needed / will work the same
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

        return $this->routeRedirectView(
            'get_profile',
            ['user' => $user->getId()],
            Response::HTTP_NO_CONTENT
        );
    }


   /**
    * @Get("/profile/{user}")
    * @ParamConverter("user", class="AppBundle:User")
    */
    public function getAction(UserInterface $user)
    {
        if ($user !== $this->getUser()) {
          throw new AccessDeniedHttpException();
        }
        return $user;        
    }
}
