<?php
namespace User\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * This view helper is used to check user permissions.
 */
class Access extends AbstractHelper 
{
    private $rbacManager = null;
    
    public function __construct($rbacManager) 
    {
        $this->rbacManager = $rbacManager;
    }
    
    public function __invoke($permission, $params = [])
    {
        return $this->rbacManager->isGranted(null, $permission, $params);
    }
}


