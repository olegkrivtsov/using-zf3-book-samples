<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Result;
use Zend\Uri\Uri;
use User\Form\LoginForm;
use User\Form\PasswordResetForm;
use User\Form\PasswordChangeForm;
use User\Entity\User;

/**
 * This controller is responsible for letting the user to log in, log out and
 * reset a forgotten password.
 */
class AuthController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager 
     */
    private $entityManager;
    
    /**
     * Auth manager.
     * @var User\Service\AuthManager 
     */
    private $authManager;
    
    /**
     * Auth service.
     * @var \Zend\Authentication\AuthenticationService
     */
    private $authService;
    
    /**
     * User manager.
     * @var User\Service\UserManager
     */
    private $userManager;
    
    /**
     * Constructor.
     */
    public function __construct($entityManager, $authManager, $authService, $userManager)
    {
        $this->entityManager = $entityManager;
        $this->authManager = $authManager;
        $this->authService = $authService;
        $this->userManager = $userManager;
    }
    
    /**
     * Authenticates user given email address and password credentials.     
     */
    public function loginAction()
    {
        // Retrieve the redirect URL (if passed). We will redirect the user to this
        // URL after successfull login.
        $redirectUrl = (string)$this->params()->fromQuery('redirectUrl', '');
        if (strlen($redirectUrl)>2048) {
            throw new \Exception("Too long redirectUrl argument passed");
        }
        
        // Check if we do not have users in database at all. If so, create 
        // the 'Admin' user.
        $this->userManager->createAdminUserIfNotExists();
        
        // Create login form
        $form = new LoginForm(); 
        $form->get('redirect_url')->setValue($redirectUrl);
        
        // Store login status.
        $isLoginError = false;
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Perform login attempt.
                $result = $this->authManager->login($data['email'], 
                        $data['password'], $data['remember_me']);
                
                // Check result.
                if ($result->getCode() == Result::SUCCESS) {
                    
                    // Get redirect URL.
                    $redirectUrl = $this->params()->fromPost('redirect_url', '');
                    
                    if (!empty($redirectUrl)) {
                        // The below check is to prevent possible redirect attack 
                        // (if someone tries to redirect user to another domain).
                        $uri = new Uri($redirectUrl);
                        if (!$uri->isValid() || $uri->getHost()!=null)
                            throw new \Exception('Incorrect redirect URL: ' . $redirectUrl);
                    }

                    // If redirect URL is provided, redirect the user to that URL;
                    // otherwise redirect to Home page.
                    if(empty($redirectUrl)) {
                        return $this->redirect()->toRoute('home');
                    } else {
                        $this->redirect()->toUrl($redirectUrl);
                    }
                } else {
                    $isLoginError = true;
                }                
            } else {
                $isLoginError = true;
            }           
        } 
        
        return new ViewModel([
            'form' => $form,
            'isLoginError' => $isLoginError,
            'redirectUrl' => $redirectUrl
        ]);
    }
    
    /**
     * The "logout" action performs logout operation.
     */
    public function logoutAction() 
    {        
        $this->authManager->logout();
        
        return $this->redirect()->toRoute('login');
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
                    return $this->redirect()->toRoute('auth', 
                            ['action'=>'message', 'id'=>'sent']);                 
                } else {
                    return $this->redirect()->toRoute('auth', 
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
            return $this->redirect()->toRoute('auth', 
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
                    return $this->redirect()->toRoute('auth', 
                            ['action'=>'message', 'id'=>'set']);                 
                } else {
                    // Redirect to "message" page
                    return $this->redirect()->toRoute('auth', 
                            ['action'=>'message', 'id'=>'failed']);                 
                }
            }               
        } 
        
        return new ViewModel([                    
            'form' => $form
        ]);
    }
}
