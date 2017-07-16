<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
* Class RestLoginController
* @RouteResource("login", pluralize=false)
*/
class RestLoginController extends FOSRestController implements ClassResourceInterface
{
   
    public function postAction()
    {
        // should never get here using Jwt 
        throw new \DomainException("Should never get this Error using JWT");
        
    }
}
