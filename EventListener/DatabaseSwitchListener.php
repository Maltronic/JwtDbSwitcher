<?php

namespace Maltronic\Bundle\JwtDbSwitcher\EventListener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityNotFoundException;
use Maltronic\Bundle\JwtDbSwitcher\Entity\AuthUser;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;

class DatabaseSwitch
{
    private $requestStack;
    private $registry;
    private $connection;
    private $tokenStorage;
    private $databases;

    /**
     * @param RequestStack $requestStack
     * @param Registry $registry
     * @param Connection $connection
     * @param TokenStorageInterface $tokenStorage
     * @param $databases
     */
    public function __construct(
        RequestStack $requestStack,
        Registry $registry,
        Connection $connection,
        TokenStorageInterface $tokenStorage,
        $databases
    ) {
        $this->requestStack = $requestStack;
        $this->registry = $registry;
        $this->connection = $connection;
        $this->tokenStorage = $tokenStorage;
        $this->databases = $databases;
    }

    /**
     * auth-profile request header used to set database
     */
    public function onKernelRequest()
    {
        if ($this->tokenStorage->getToken()) {
            if ($this->requestStack->getMasterRequest()->headers->has('auth-profile')) {
                $requestedDbName = $this->requestStack->getMasterRequest()->headers->get('auth-profile');
                $params = $this->connection->getParams();

                if ($requestedDbName != $params['dbname']) {
                    $params['driver'] = $this->databases[$requestedDbName . '_database_driver'];
                    $params['host'] = $this->databases[$requestedDbName . '_database_host'];
                    $params['port'] = $this->databases[$requestedDbName . '_database_port'];
                    $params['dbname'] = $this->databases[$requestedDbName . '_database_name'];
                    $params['user'] = $this->databases[$requestedDbName . '_database_user'];
                    $params['password'] = $this->databases[$requestedDbName . '_database_password'];

                    if ($this->connection->isConnected()) {
                        $this->connection->close();
                    }
                    $this->connection->__construct(
                        $params,
                        $this->connection->getDriver(),
                        $this->connection->getConfiguration(),
                        $this->connection->getEventManager()
                    );
                    $this->connection->connect();
                    $this->registry->resetManager('default');

                    /** @var AuthUser $authUser */
                    $authUser = $this->registry->getManager('default')
                        ->getRepository('AppBundle:User')->findOneBy(
                            array('username' => $this->tokenStorage->getToken()->getUsername())
                        );

                    if (is_null($authUser)) {
                        throw new EntityNotFoundException('User not found in requested database');
                    }

                    $jwtUserToken = new JWTUserToken($authUser->getRoles());
                    $jwtUserToken->setUser($authUser);
                    $this->tokenStorage->setToken($jwtUserToken);
                }
            }
        }
    }
}
