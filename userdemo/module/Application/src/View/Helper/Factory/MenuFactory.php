<?php
namespace Application\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\View\Helper\Menu;
use Application\Service\NavManager;

/**
 * This is the factory for Menu view helper. Its purpose is to instantiate the
 * helper and init menu items.
 */
class MenuFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $navManager = $container->get(NavManager::class);
        
        // Get menu items.
        $items = $navManager->getMenuItems();
        
        // Instantiate the helper.
        return new Menu($items);
    }
}

