<?php

namespace ProspectOne\UserModule\Controller\Factory;


use Interop\Container\ContainerInterface;
use ProspectOne\UserModule\Controller\ConsoleController;
use ProspectOne\UserModule\Service\UserManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ConsoleControllerFactory
 * @package ProspectOne\UserModule\Controller\Factory
 */
class ConsoleControllerFactory implements FactoryInterface
{
    /**
     * Create ConsoleController
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return ConsoleController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $userManager = $container->get(UserManager::class);
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        return new ConsoleController($userManager, $entityManager);
    }
}
