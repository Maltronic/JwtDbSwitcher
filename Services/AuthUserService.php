<?php

namespace Maltronic\Bundle\JwtDbSwitcher\Services;

use Maltronic\Bundle\JwtDbSwitcher\Entity\AuthUser;
use Doctrine\ORM\EntityManager;
use Maltronic\Bundle\JwtDbSwitcher\Entity\AuthUserRepository as Repository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Maltronic\Bundle\JwtDbSwitcher\Entity\Database;

class AuthUserService
{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * Entity manager
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var
     */
    protected $passwordEncoder;

    /**
     * Class constructor
     *
     * @param EntityManager                $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return self
     */
    public function __construct(EntityManager $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->repository = $entityManager->getRepository('Maltronic\Bundle\JwtDbSwitcher\Entity\AuthUser');
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Get array of profiles for username
     *
     * @param string $username
     *
     * @return ArrayCollection|array
     */
    public function getProfiles($username)
    {
        /** @var AuthUser $authUser */
        $authUser = $this->repository->findOneBy(['username' => $username]);
        if ($authUser) {
            return $authUser->getDatabases();
        }

        return [];
    }

    /**
     * Create a new AuthUser
     *
     * @param array $data
     *
     * @return AuthUser
     */
    public function createAuthUser($data)
    {
        $user = new AuthUser();
        $user->setUsername($data['username']);

        if (count($data['databases']) > 0) {
            foreach ($data['databases'] as $databases) {
                if ($databases instanceof Database) {
                    $user->addDatabase($databases);
                }
            }
        }

        $password = $this->encodePassword($user, $data['password']);
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     *
     * @param AuthUser $user the user or the logged in user if null
     * @param string   $password a password or randomly generated if null
     *
     * @return string the new password
     */
    public function modifyPassword(AuthUser $user, $password = null)
    {
        $password = $this->encodePassword($user, $password);
        $user->setPassword($password);
        $user->setPasswordResetRequired(false);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $password;
    }

    /**
     * Encode the password
     *
     * @param AuthUser $user
     * @param string   $password
     *
     * @return string
     */
    protected function encodePassword(AuthUser $user, $password)
    {
        return $this->passwordEncoder->encodePassword($user, $password);
    }
}
