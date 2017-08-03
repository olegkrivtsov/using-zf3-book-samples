<?php

namespace ProspectOne\UserModule\Form\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use ProspectOne\UserModule\Entity\User;
use ProspectOne\UserModule\Form\UserForm;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class UserFormFactory
 * @package ProspectOne\UserModule\Form\Factory
 */
class UserFormFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if(empty($options)) {
            return new UserForm();
        }

        return new UserForm(...$options);
    }
}
