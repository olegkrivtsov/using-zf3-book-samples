<?php
namespace ProspectOne\UserModule\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\Exception\RuntimeException;
use Zend\Session\SessionManager;
use Zend\Authentication\Storage\Session as SessionStorage;
use ProspectOne\UserModule\Service\AuthAdapter;

/**
 * The factory responsible for creating of authentication service.
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * This method creates the Zend\Authentication\AuthenticationService service 
     * and returns its instance.
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AuthenticationService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var SessionManager $sessionManager */
        $sessionManager = $container->get(SessionManager::class);
        try {
            $sessionManager->start();
        } catch (RuntimeException $e) {
            session_unset();
            $sessionManager->start();
        }
        try {
            $authStorage = new SessionStorage('Zend_Auth', 'session', $sessionManager);
        } catch (ServiceNotCreatedException $e) {
            session_unset();
            $authStorage = new SessionStorage('Zend_Auth', 'session', $sessionManager);
        }
        $authAdapter = $container->get(AuthAdapter::class);

        // Create the service and inject dependencies into its constructor.
        return new AuthenticationService($authStorage, $authAdapter);
    }
}

