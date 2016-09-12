<?php
namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Service\PostManager;
use Application\Controller\PostController;

/**
 * This is the factory for PostController. Its purpose is to instantiate the
 * controller.
 */
class PostControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $postManager = $container->get(PostManager::class);
        
        // Instantiate the controller and inject dependencies
        return new PostController($entityManager, $postManager);
    }
}


