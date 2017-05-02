<?php
namespace User\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * This controller plugin is used for role-based access control (RBAC).
 */
class AccessPlugin extends AbstractPlugin
{
    private $rbacManager;
    
    public function __construct($rbacManager)
    {
        $this->rbacManager = $rbacManager;
    }
    
    /**
     * Checks whether the currently logged in user has the given permission.
     * @param string $permission Permission name.
     * @param array $params Optional params (used only if an assertion is associated with permission).
     */
    public function __invoke($permission, $params = [])
    {
        return $this->rbacManager->isGranted(null, $permission, $params);
    }
}


