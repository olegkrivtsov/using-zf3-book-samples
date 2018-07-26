<?php
namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Service\MailSender;
use Application\Controller\IndexController;

/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller.
 */
class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $mailSender = $container->get(MailSender::class);
        $i18nSessionContainer = $container->get('I18nSessionContainer');
        
        // Instantiate the controller and inject dependencies
        return new IndexController($mailSender, $i18nSessionContainer);
    }
}

