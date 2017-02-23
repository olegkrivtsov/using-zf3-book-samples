<?php

namespace ProspectOne\UserModule\Factory;

use Interop\Container\ContainerInterface;
use Zend\Crypt\Password\Bcrypt;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class Bcrypt
 * @package ProspectOne\UserModule\Factory
 */
class BcryptFactory implements FactoryInterface
{

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return Bcrypt
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $cost = $config['ProspectOne\UserModule']['bcrypt']['cost'];
        $bcrypt = new Bcrypt();
        $bcrypt->setCost($cost);
        return $bcrypt;
    }
}
