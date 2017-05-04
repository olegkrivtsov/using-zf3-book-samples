<?php
namespace ProspectOne\UserModule\Service\Factory;

use Interop\Container\ContainerInterface;
use ProspectOne\UserModule\Service\AuthAdapter;
use Zend\Crypt\Password\Bcrypt;
use Zend\Http\PhpEnvironment\Request;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory class for AuthAdapter service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class AuthAdapterFactory implements FactoryInterface
{
    /**
     * This method creates the AuthAdapter service and returns its instance.
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AuthAdapter
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        // Get Doctrine entity manager from Service Manager.
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        /** @var Bcrypt $bcrypt */
        $bcrypt = $container->get('ProspectOne\UserModule\Bcrypt');

        $config = $container->get('Config');
        $headerEnabled = $config['ProspectOne\UserModule']['auth']['header'];

        /** @var Request $request */
        $request = $container->get("Request");
        if ($headerEnabled && $request instanceof Request) {
            $header = $request->getHeaders()->get($config['ProspectOne\UserModule']['auth']['header_name']);
            if ($header !== false) {
                $header = $header->getFieldValue();
            } else {
                $header = null;
            }
        } else {
            $header = null;
        }
                        
        // Create the AuthAdapter and inject dependency to its constructor.
        return new AuthAdapter($entityManager, $bcrypt, $headerEnabled, $header);
    }
}
