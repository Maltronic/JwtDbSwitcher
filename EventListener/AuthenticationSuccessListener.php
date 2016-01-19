<?php

namespace Maltronic\Bundle\JwtDbSwitcher\EventListener;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Maltronic\Bundle\JwtDbSwitcher\Entity\AuthUser;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AuthenticationSuccessListener
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
     * Add unsigned parameters, such as:
     * - databases (this list is also signed but presented here for listing to unauthenticated clients)
     * - parameters, extra info you may attach to object as necessary
     *
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();

        $AuthUser = $event->getUser();
        if (!($AuthUser instanceof AuthUser)) {
            throw new AccessDeniedException('AuthUser object not found');
        }

        if (!in_array('ROLE_USER', $AuthUser->getRoles())) {
            throw new AccessDeniedException('User lacks necessary role');
        }

        $AuthUser->setLastToken($data['token']);
        $AuthUser = $this->entityManager->merge($AuthUser);
        $this->entityManager->persist($AuthUser);
        $this->entityManager->flush();

        $data['databases'] = $this->getDatabaseArray($event->getUser());
        $data['parameters'] = $this->getParametersArray($event->getUser());

        $event->setData($data);
    }

    protected function getDatabaseArray(AuthUser $user)
    {
        $databases =  [];

        foreach ($user->getDatabases() as $database) {
            $databases[] = array(
                'id' => $database->getName(),
                'name' => $database->getDisplayName(),
                'parameters' => $this->getParametersArray($database->getParameters())
            );
        }

        return $databases;
    }

    protected function getParametersArray(Collection $parameters)
    {
        $returnArray = [];
        foreach ($parameters as $parameter) {
            $returnArray[$parameter->getField()] = $parameter->getValue();
        }

        return $returnArray;
    }
}
