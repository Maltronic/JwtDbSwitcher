<?php

namespace Maltronic\Bundle\JwtDbSwitcher\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Maltronic\Bundle\JwtDbSwitcher\Entity\AuthUser;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class JWTCreatedListener
{

    /**
     * Adds security info to the signed token
     *
     * @param JWTCreatedEvent $event
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        if (!($request = $event->getRequest())) {
            return;
        }

        /**
         * @var AuthUser $user
         */
        $user = $event->getUser();

        if (!$user->getIsActive()) {
            throw new AccessDeniedException('User is not active');
        }

        $databases = array();
        foreach ($user->getDatabases() as $database) {
            $databases[] = $database->getName();
        }

        $payload = $event->getData();
        $payload['ip'] = $request->getClientIp();
        $payload['databases'] = $databases;

        $event->setData($payload);
    }
}
