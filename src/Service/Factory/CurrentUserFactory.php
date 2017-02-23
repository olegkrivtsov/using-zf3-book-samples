<?php

namespace ProspectOne\UserModule\Service\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use ProspectOne\UserModule\Entity\User;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Authentication\AuthenticationService;
use BadMethodCallException;

/**
 * Class CurrentUserFactory
 * @package ProspectOne\UserModule\Service\Factory
 */
class CurrentUserFactory implements FactoryInterface
{
    /**
     * Create an currently logged in user object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return User
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : User
    {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        /** @var AuthenticationService $authService */
        $authService = $container->get(AuthenticationService::class);
        $email = $authService->getIdentity();
        if (empty($email)) {
            throw new BadMethodCallException("Can be created for only logged in users");
        }
        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findBy(['email' => $email])[0];
        return $user;
    }
}
