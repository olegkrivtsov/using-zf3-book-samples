<?php
namespace User\Service;

use User\Entity\Permission;

/**
 * This service is responsible for adding/editing permissions.
 */
class PermissionManager
{
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;  
    
    /**
     * RBAC manager.
     * @var User\Service\RbacManager
     */
    private $rbacManager;
    
    /**
     * Constructs the service.
     */
    public function __construct($entityManager, $rbacManager) 
    {
        $this->entityManager = $entityManager;
        $this->rbacManager = $rbacManager;
    }
    
    /**
     * Adds a new permission.
     * @param array $data
     */
    public function addPermission($data)
    {
        $existingPermission = $this->entityManager->getRepository(Permission::class)
                ->findOneByName($data['name']);
        if ($existingPermission!=null) {
            throw new \Exception('Permission with such name already exists');
        }
        
        $permission = new Permission();
        $permission->setName($data['name']);
        $permission->setDescription($data['description']);
        $permission->setDateCreated(date('Y-m-d H:i:s'));
        
        $this->entityManager->persist($permission);
        
        $this->entityManager->flush();
        
        // Reload RBAC container.
        $this->rbacManager->init(true);
    }
    
    /**
     * Updates an existing permission.
     * @param Permission $permission
     * @param array $data
     */
    public function updatePermission($permission, $data)
    {
        $existingPermission = $this->entityManager->getRepository(Permission::class)
                ->findOneByName($data['name']);
        if ($existingPermission!=null && $existingPermission!=$permission) {
            throw new \Exception('Another permission with such name already exists');
        }
        
        $permission->setName($data['name']);
        $permission->setDescription($data['description']);
        
        $this->entityManager->flush();
        
        // Reload RBAC container.
        $this->rbacManager->init(true);
    }
    
    /**
     * Deletes the given permission.
     */
    public function deletePermission($permission)
    {
        $this->entityManager->remove($permission);
        $this->entityManager->flush();
        
        // Reload RBAC container.
        $this->rbacManager->init(true);
    }
    
    /**
     * This method creates the default set of permissions if no permissions exist at all.
     */
    public function createDefaultPermissionsIfNotExist()
    {
        $permission = $this->entityManager->getRepository(Permission::class)
                ->findOneBy([]);
        if ($permission!=null)
            return; // Some permissions already exist; do nothing.
        
        $defaultPermissions = [
            'user.manage' => 'Manage users',
            'permission.manage' => 'Manage permissions',
            'role.manage' => 'Manage roles',
            'profile.any.view' => 'View anyone\'s profile',
            'profile.own.view' => 'View own profile',
        ];
        
        foreach ($defaultPermissions as $name=>$description) {
            $permission = new Permission();
            $permission->setName($name);
            $permission->setDescription($description);
            $permission->setDateCreated(date('Y-m-d H:i:s'));
            
            $this->entityManager->persist($permission);
        }
        
        $this->entityManager->flush();
        
        // Reload RBAC container.
        $this->rbacManager->init(true);
    }
}

