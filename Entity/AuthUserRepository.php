<?php

namespace Maltronic\Bundle\JwtDbSwitcher\Entity;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Doctrine\ORM\EntityRepository;

class AuthUserRepository extends EntityRepository implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {

        $user = $this->createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $user) {
            $message = sprintf(
                'Authentication User "%s" not found.',
                $username
            );
            throw new UsernameNotFoundException($message);
        }


        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Unsupported instance: "%s".',
                    $class
                )
            );
        }

        return $this->findOneBy(array("username" => $user->getUsername()));
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class
        || is_subclass_of($class, $this->getEntityName());
    }
}
