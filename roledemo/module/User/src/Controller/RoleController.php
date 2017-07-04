<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Entity\Role;
use User\Entity\Permission;
use User\Form\RoleForm;
use User\Form\RolePermissionsForm;

/**
 * This controller is responsible for role management (adding, editing, 
 * viewing, deleting).
 */
class RoleController extends AbstractActionController 
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /**
     * Role manager.
     * @var User\Service\RoleManager 
     */
    private $roleManager;
    
    /**
     * Constructor. 
     */
    public function __construct($entityManager, $roleManager)
    {
        $this->entityManager = $entityManager;
        $this->roleManager = $roleManager;
    }
    
    /**
     * This is the default "index" action of the controller. It displays the 
     * list of roles.
     */
    public function indexAction() 
    {
        $roles = $this->entityManager->getRepository(Role::class)
                ->findBy([], ['id'=>'ASC']);
        
        return new ViewModel([
            'roles' => $roles
        ]);
    } 
    
    /**
     * This action displays a page allowing to add a new role.
     */
    public function addAction()
    {
        // Create form
        $form = new RoleForm('create', $this->entityManager);
        
        $roleList = [];
        $roles = $this->entityManager->getRepository(Role::class)
                ->findBy([], ['name'=>'ASC']);
        foreach ($roles as $role) {
            $roleList[$role->getId()] = $role->getName();
        }
        $form->get('inherit_roles[]')->setValueOptions($roleList);
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Add role.
                $this->roleManager->addRole($data);
                
                // Add a flash message.
                $this->flashMessenger()->addSuccessMessage('Added new role.');
                
                // Redirect to "index" page
                return $this->redirect()->toRoute('roles', ['action'=>'index']);                
            }               
        } 
        
        return new ViewModel([
                'form' => $form
            ]);
    }
    
    /**
     * The "view" action displays a page allowing to view role's details.
     */
    public function viewAction() 
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Find a role with such ID.
        $role = $this->entityManager->getRepository(Role::class)
                ->find($id);
        
        if ($role == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $allPermissions = $this->entityManager->getRepository(Permission::class)
                ->findBy([], ['name'=>'ASC']);
        
        $effectivePermissions = $this->roleManager->getEffectivePermissions($role);
                
        return new ViewModel([
            'role' => $role,
            'allPermissions' => $allPermissions,
            'effectivePermissions' => $effectivePermissions
        ]);
    }
    
    /**
     * This action displays a page allowing to edit an existing role.
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $role = $this->entityManager->getRepository(Role::class)
                ->find($id);
        
        if ($role == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Create form
        $form = new RoleForm('update', $this->entityManager, $role);
        
        $roleList = [];
        $roles = $this->entityManager->getRepository(Role::class)
                ->findBy([], ['name'=>'ASC']);
        foreach ($roles as $role2) {
            $roleList[$role2->getId()] = $role2->getName();
        }
        $form->get('inherit_roles[]')->setValueOptions($roleList);
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Update permission.
                $this->roleManager->updateRole($role, $data);
                
                // Add a flash message.
                $this->flashMessenger()->addSuccessMessage('Updated the role.');
                
                // Redirect to "index" page
                return $this->redirect()->toRoute('roles', ['action'=>'index']);                
            }               
        } else {
            $form->setData(array(
                    'name'=>$role->getName(),
                    'description'=>$role->getDescription()     
                ));
        }
        
        return new ViewModel([
                'form' => $form,
                'role' => $role
            ]);
    }
    
    /**
     * The "editPermissions" action allows to edit permissions assigned to the given role.
     */
    public function editPermissionsAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $role = $this->entityManager->getRepository(Role::class)
                ->find($id);
        
        if ($role == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
            
        $allPermissions = $this->entityManager->getRepository(Permission::class)
                ->findBy([], ['name'=>'ASC']);
        
        $effectivePermissions = $this->roleManager->getEffectivePermissions($role);
            
        // Create form
        $form = new RolePermissionsForm($this->entityManager);
        foreach ($allPermissions as $permission) {
            $label = $permission->getName();
            $isDisabled = false;
            if (isset($effectivePermissions[$permission->getName()]) && $effectivePermissions[$permission->getName()]=='inherited') {
                $label .= ' (inherited)';
                $isDisabled = true;
            }
            $form->addPermissionField($permission->getName(), $label, $isDisabled);
        }
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Update permissions.
                $this->roleManager->updateRolePermissions($role, $data);
                
                // Add a flash message.
                $this->flashMessenger()->addSuccessMessage('Updated permissions for the role.');
                
                // Redirect to "index" page
                return $this->redirect()->toRoute('roles', ['action'=>'view', 'id'=>$role->getId()]);                
            }
        } else {
        
            $data = [];
            foreach ($effectivePermissions as $name=>$inherited) {
                $data['permissions'][$name] = 1;
            }
            
            $form->setData($data);
        }
        
        $errors = $form->getMessages();
        
        return new ViewModel([
                'form' => $form,
                'role' => $role,
                'allPermissions' => $allPermissions,
                'effectivePermissions' => $effectivePermissions
            ]);
    }

    /**
     * This action deletes a permission.
     */
    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $role = $this->entityManager->getRepository(Role::class)
                ->find($id);
        
        if ($role == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Delete role.
        $this->roleManager->deleteRole($role);
        
        // Add a flash message.
        $this->flashMessenger()->addSuccessMessage('Deleted the role.');

        // Redirect to "index" page
        return $this->redirect()->toRoute('roles', ['action'=>'index']); 
    }
}




