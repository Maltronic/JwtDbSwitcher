<?php

namespace Maltronic\Bundle\JwtDbSwitcher\EventListener;

use Doctrine\ORM\EntityManager;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;

class JWTDecodedListener
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Check security info and reject if invalid
     *
     * @param JWTDecodedEvent $event
     * @return void
     */
    public function onJWTDecoded(JWTDecodedEvent $event)
    {
        $request = $event->getRequest();
        $payload = $event->getPayload();

        if (empty($payload['username'])) {
            $event->markAsInvalid();
            return;
        }

        if (!$token = substr($request->headers->get('Authorization'), 7)) {
            $event->markAsInvalid();
            return;
        }

        if (!$this->validateUser($payload['username'], $token)) {
                $event->markAsInvalid();
                return;
        }

        $requestedDatabase = $request->headers->get('x-database');
        if (is_null($requestedDatabase)) {
            $event->markAsInvalid();
            return;
        }


        if (empty($payload['databases'])) {
            $event->markAsInvalid();
            return;
        }

        if (!$this->validateAttributes($requestedDatabase, $payload, $request->getClientIp())) {
            $event->markAsInvalid();
            return;
        }
    }

    protected function validateUser($username, $token)
    {
        // Check token is the latest one issued for the user
        $AuthUser = $this->entityManager
            ->getRepository('JwtDbSwitcher:AuthUser')->findOneBy(
                array('username' => $username)
            );

        if (is_null($AuthUser)) {
            return false;
        }

        if (!$AuthUser->getIsActive()) {
            return false;
        }

        if ($token != $AuthUser->getLastToken()) {
            return false;
        }

        return true;
    }

    protected function validateAttributes($requestSite, $payload, $ipAddress)
    {
        $return = true;

        if (!in_array($requestSite, $payload['databases'])) {
            $return = false;
        }

        if (!isset($payload['ip']) || $payload['ip'] !== $ipAddress) {
            $return = false;
        }

        return $return;
    }
}
