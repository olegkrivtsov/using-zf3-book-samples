<?php
namespace User\Service;

use User\Entity\Role;
use User\Entity\Permission;

/**
 * This service is responsible for adding/editing roles.
 */
class RoleManager
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
     * Adds a new role.
     * @param array $data
     */
    public function addRole($data)
    {
        $existingRole = $this->entityManager->getRepository(Role::class)
                ->findOneByName($data['name']);
        if ($existingRole!=null) {
            throw new \Exception('Role with such name already exists');
        }
        
        $role = new Role;
        $role->setName($data['name']);
        $role->setDescription($data['description']);
        $role->setDateCreated(date('Y-m-d H:i:s'));
        
        $this->entityManager->persist($role);
        
        // Apply changes to database.
        $this->entityManager->flush();
        
        // Reload RBAC container.
        $this->rbacManager->init(true);
    }
    
    /**
     * Updates an existing role.
     * @param Role $role
     * @param array $data
     */
    public function updateRole($role, $data)
    {
        $existingRole = $this->entityManager->getRepository(Role::class)
                ->findOneByName($data['name']);
        if ($existingRole!=null && $existingRole!=$role) {
            throw new \Exception('Another role with such name already exists');
        }
        
        $role->setName($data['name']);
        $role->setDescription($data['description']);
        
        $this->entityManager->flush();
        
        // Reload RBAC container.
        $this->rbacManager->init(true);
    }
    
    /**
     * Deletes the given role.
     */
    public function deleteRole($role)
    {
        $this->entityManager->remove($role);
        $this->entityManager->flush();
        
        // Reload RBAC container.
        $this->rbacManager->init(true);
    }
    
    /**
     * This method creates the default set of roles if no roles exist at all.
     */
    public function createDefaultRolesIfNotExist()
    {
        $role = $this->entityManager->getRepository(Role::class)
                ->findOneBy([]);
        if ($role!=null)
            return; // Some roles already exist; do nothing.
        
        $defaultRoles = [
            'Administrator' => [
                'description' => 'A person who manages users, roles, etc.',
                'parent' => null,
                'permissions' => [
                    'user.manage',
                    'role.manage',
                    'permission.manage',
                    'profile.any.view',
                ],
            ],
            'Guest' => [
                'description' => 'A person who can log in and view own profile.',
                'parent' => null,
                'permissions' => [
                    'profile.own.view',
                ],
            ],
        ];
        
        foreach ($defaultRoles as $name=>$info) {
            
            // Create new role
            $role = new Role();
            $role->setName($name);
            $role->setDescription($info['description']);
            $role->setDateCreated(date('Y-m-d H:i:s'));
            
            // Assign parent role
            if ($info['parent']!=null) {
                $parentRole = $this->entityManager->getRepository(Role::class)
                        ->findOneByName($info['parent']);
                if ($parentRole==null) {
                    throw new \Exception('Parent role ' . $info['parent'] . ' doesn\'t exist');
                }
                
                $role->setParentRole($parentRole);
            }
            
            $this->entityManager->persist($role);
            
            // Assign permissions to role
            $permissions = $this->entityManager->getRepository(Permission::class)
                    ->findByName($info['permissions']);
            foreach ($permissions as $permission) {
                $role->getPermissions()->add($permission);
            }
        }
        
        // Apply changes to database.
        $this->entityManager->flush();
        
        // Reload RBAC container.
        $this->rbacManager->init(true);
    }
    
    /**
     * Retrieves all permissions from the given role and its child roles.
     * @param Role $role
     */
    public function getEffectivePermissions($role)
    {
        $effectivePermissions = [];
        
        foreach ($role->getChildRoles() as $childRole)
        {
            $childPermissions = $this->getEffectivePermissions($childRole);
            foreach ($childPermissions as $name=>$inherited) {
                $effectivePermissions[$name] = 'inherited';
            }
        }
        
        foreach ($role->getPermissions() as $permission)
        {
            if (!isset($effectivePermissions[$permission->getName()])) {
                $effectivePermissions[$permission->getName()] = 'own';
            }
        }
        
        return $effectivePermissions;
    }
    
    /**
     * Updates permissions of a role. 
     */
    public function updateRolePermissions($role, $data)
    {
        // Remove old permissions.
        $role->getPermissions()->clear();
        
        // Assign new permissions to role
        foreach ($data['permissions'] as $name=>$isChecked) {
            if (!$isChecked)
                continue;
            
            $permission = $this->entityManager->getRepository(Permission::class)
                ->findOneByName($name);
            if ($permission == null) {
                throw new \Exception('Permission with such name doesn\'t exist');
            }
            
            $role->getPermissions()->add($permission);            
        }
        
        // Apply changes to database.
        $this->entityManager->flush();
        
        // Reload RBAC container.
        $this->rbacManager->init(true);
    }
}

