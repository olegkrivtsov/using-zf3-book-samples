<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Entity\Permission;
use User\Form\PermissionForm;

/**
 * This controller is responsible for permission management (adding, editing, 
 * viewing, deleting).
 */
class PermissionController extends AbstractActionController 
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /**
     * Permission manager.
     * @var User\Service\PermissionManager 
     */
    private $permissionManager;
    
    /**
     * Constructor. 
     */
    public function __construct($entityManager, $permissionManager)
    {
        $this->entityManager = $entityManager;
        $this->permissionManager = $permissionManager;
    }
    
    /**
     * This is the default "index" action of the controller. It displays the 
     * list of permission.
     */
    public function indexAction() 
    {
        $permissions = $this->entityManager->getRepository(Permission::class)
                ->findBy([], ['name'=>'ASC']);
        
        return new ViewModel([
            'permissions' => $permissions
        ]);
    } 
    
    /**
     * This action displays a page allowing to add a new permission.
     */
    public function addAction()
    {
        // Create form
        $form = new PermissionForm('create', $this->entityManager);
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Add permission.
                $this->permissionManager->addPermission($data);
                
                // Add a flash message.
                $this->flashMessenger()->addSuccessMessage('Added new permission.');
                
                // Redirect to "index" page
                return $this->redirect()->toRoute('permissions', ['action'=>'index']);                
            }               
        } 
        
        return new ViewModel([
                'form' => $form
            ]);
    }
    
    /**
     * The "view" action displays a page allowing to view permission's details.
     */
    public function viewAction() 
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Find a permission with such ID.
        $permission = $this->entityManager->getRepository(Permission::class)
                ->find($id);
        
        if ($permission == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
                
        return new ViewModel([
            'permission' => $permission
        ]);
    }
    
    /**
     * This action displays a page allowing to edit an existing permission.
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $permission = $this->entityManager->getRepository(Permission::class)
                ->find($id);
        
        if ($permission == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Create form
        $form = new PermissionForm('update', $this->entityManager, $permission);
        
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
                $this->permissionManager->updatePermission($permission, $data);
                
                // Add a flash message.
                $this->flashMessenger()->addSuccessMessage('Updated the permission.');
                
                // Redirect to "index" page
                return $this->redirect()->toRoute('permissions', ['action'=>'index']);                
            }               
        } else {
            $form->setData(array(
                    'name'=>$permission->getName(),
                    'description'=>$permission->getDescription()     
                ));
        }
        
        return new ViewModel([
                'form' => $form,
                'permission' => $permission
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
        
        $permission = $this->entityManager->getRepository(Permission::class)
                ->find($id);
        
        if ($permission == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Delete permission.
        $this->permissionManager->deletePermission($permission);
        
        // Add a flash message.
        $this->flashMessenger()->addSuccessMessage('Deleted the permission.');

        // Redirect to "index" page
        return $this->redirect()->toRoute('permissions', ['action'=>'index']); 
    }
}






