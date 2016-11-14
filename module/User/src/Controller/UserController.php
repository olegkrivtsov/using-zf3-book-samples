<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Entity\User;
use User\Form\UserForm;
use User\Form\PasswordChangeForm;
use User\Form\PasswordResetForm;

/**
 * This controller is responsible for user management (adding, editing, 
 * viewing users and changing user's password).
 */
class UserController extends AbstractActionController 
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /**
     * User manager.
     * @var User\Service\UserManager 
     */
    private $userManager;
    
    /**
     * Constructor. 
     */
    public function __construct($entityManager, $userManager)
    {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
    }
    
    /**
     * This is the default "index" action of the controller. It displays the 
     * list of users.
     */
    public function indexAction() 
    {
        $users = $this->entityManager->getRepository(User::class)
                ->findBy([], ['id'=>'ASC']);
        
        return new ViewModel([
            'users' => $users
        ]);
    } 
    
    /**
     * This action displays a page allowing to add a new user.
     */
    public function addAction()
    {
        // Create user form
        $form = new UserForm('create', $this->entityManager);
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Add user.
                $user = $this->userManager->addUser($data);
                
                // Redirect to "view" page
                return $this->redirect()->toRoute('users', 
                        ['action'=>'view', 'id'=>$user->getId()]);                
            }               
        } 
        
        return new ViewModel([
                'form' => $form
            ]);
    }
    
    /**
     * The "view" action displays a page allowing to view user's details.
     */
    public function viewAction() 
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Find a user with such ID.
        $user = $this->entityManager->getRepository(User::class)
                ->find($id);
        
        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
                
        return new ViewModel([
            'user' => $user
        ]);
    }
    
    /**
     * The "edit" action displays a page allowing to edit user.
     */
    public function editAction() 
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $user = $this->entityManager->getRepository(User::class)
                ->find($id);
        
        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Create user form
        $form = new UserForm('update', $this->entityManager, $user);
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Update the user.
                $this->userManager->updateUser($user, $data);
                
                // Redirect to "view" page
                return $this->redirect()->toRoute('users', 
                        ['action'=>'view', 'id'=>$user->getId()]);                
            }               
        } else {
            $form->setData(array(
                    'full_name'=>$user->getFullName(),
                    'email'=>$user->getEmail(),
                    'status'=>$user->getStatus(),                    
                ));
        }
        
        return new ViewModel(array(
            'user' => $user,
            'form' => $form
        ));
    }
    
    /**
     * This action displays a page allowing to change user's password.
     */
    public function changePasswordAction() 
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $user = $this->entityManager->getRepository(User::class)
                ->find($id);
        
        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Create "change password" form
        $form = new PasswordChangeForm('change');
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Try to change password.
                if (!$this->userManager->changePassword($user, $data)) {
                    $this->flashMessenger()->addErrorMessage(
                            'Sorry, the old password is incorrect. Could not set the new password.');
                } else {
                    $this->flashMessenger()->addSuccessMessage(
                            'Changed the password successfully.');
                }
                
                // Redirect to "view" page
                return $this->redirect()->toRoute('users', 
                        ['action'=>'view', 'id'=>$user->getId()]);                
            }               
        } 
        
        return new ViewModel([
            'user' => $user,
            'form' => $form
        ]);
    }
    
    /**
     * This action displays the "Reset Password" page.
     */
    public function resetPasswordAction()
    {
        // Create form
        $form = new PasswordResetForm();
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Look for the user with such email.
                $user = $this->entityManager->getRepository(User::class)
                        ->findOneByEmail($data['email']);                
                if ($user!=null) {
                    // Generate a new password for user and send an E-mail 
                    // notification about that.
                    $this->userManager->generatePasswordResetToken($user);
                    
                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users', 
                            ['action'=>'message', 'id'=>'sent']);                 
                } else {
                    return $this->redirect()->toRoute('users', 
                            ['action'=>'message', 'id'=>'invalid-email']);                 
                }
            }               
        } 
        
        return new ViewModel([                    
            'form' => $form
        ]);
    }
    
    /**
     * This action displays an informational message page. 
     * For example "Your password has been resetted" and so on.
     */
    public function messageAction() 
    {
        // Get message ID from route.
        $id = (string)$this->params()->fromRoute('id');
        
        // Validate input argument.
        if($id!='invalid-email' && $id!='sent' && $id!='set' && $id!='failed') {
            throw new \Exception('Invalid message ID specified');
        }
        
        return new ViewModel([
            'id' => $id
        ]);
    }
    
    /**
     * This action displays the "Reset Password" page. 
     */
    public function setPasswordAction()
    {
        $token = $this->params()->fromRoute('token', null);
        
        // Validate token length
        if ($token!=null && (!is_string($token) || strlen($token)!=32)) {
            throw new \Exception('Invalid token type or length');
        }
        
        if($token===null || 
           !$this->userManager->validatePasswordResetToken($token)) {
            return $this->redirect()->toRoute('user', 
                    ['action'=>'message', 'id'=>'failed']);
        }
                
        // Create form
        $form = new PasswordChangeForm('reset');
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                $data = $form->getData();
                                               
                // Set new password for the user.
                if ($this->userManager->setPasswordByToken($token, $data['password'])) {
                    
                    // Redirect to "message" page
                    return $this->redirect()->toRoute('user', 
                            ['action'=>'message', 'id'=>'set']);                 
                } else {
                    // Redirect to "message" page
                    return $this->redirect()->toRoute('user', 
                            ['action'=>'message', 'id'=>'failed']);                 
                }
            }               
        } 
        
        return new ViewModel([                    
            'form' => $form
        ]);
    }
}


