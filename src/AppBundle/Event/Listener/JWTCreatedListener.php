<?php

// /src/AppBundle/Event/Listener/JWTCreatedListener.php

namespace AppBundle\Event\Listener;

use AppBundle\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class JWTCreatedListener
{
    /**
     * Replaces the data in the generated
     *
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        /** @var $user User */
        $user = $event->getUser();

        // add new data
        $payload['userId'] = $user->getId();
        $payload['username'] = $user->getUsername();

        $event->setData($payload);
    }
}