<?php
namespace User\Service;

use Zend\Authentication\Result;

/**
 * The AuthManager service is responsible for user login/logout and simple access 
 * filtering. The access filtering feature checks whether the currently logged in
 * user is allowed to see the given page or not. 
 */
class AuthManager
{
    /**
     * Authentication service.
     * @var \Zend\Authentication\AuthenticationService
     */
    private $authService;
    
    /**
     * Session manager.
     * @var Zend\Session\SessionManager
     */
    private $sessionManager;
    
    /**
     * Contents of the 'access_filter' config key.
     * @var array 
     */
    private $config;
    
    /**
     * Constructs the service.
     */
    public function __construct($authService, $sessionManager, $config) 
    {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
        $this->config = $config;
    }
    
    /**
     * Performs a login attempt. If $rememberMe argument is true, it forces the session
     * to last for one month (otherwise the session expires on one hour).
     */
    public function login($email, $password, $rememberMe)
    {   
        // Check if user has already logged in. If so, do not allow to log in 
        // twice.
        if ($this->authService->getIdentity()!=null) {
            throw new \Exception('Already logged in');
        }
            
        // Authenticate with login/password.
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setEmail($email);
        $authAdapter->setPassword($password);
        $result = $this->authService->authenticate();

        // If user wants to "remember him", we will make session to expire in 
        // one month. By default session expires in 1 hour (as specified in our 
        // config/global.php file).
        if ($result->getCode()==Result::SUCCESS && $rememberMe) {
            // Session will expire in 1 month (30 days).
            $this->sessionManager->rememberMe(60*60*24*30);
        }
        
        return $result;
    }
    
    /**
     * Performs user logout.
     */
    public function logout()
    {
        // Allow to log out only when user is logged in.
        if ($this->authService->getIdentity()==null) {
            throw new \Exception('The user is not logged in');
        }
        
        // Remove identity from session.
        $this->authService->clearIdentity();               
    }
    
    /**
     * This method uses the 'access_filter' key in the config file and determines
     * whenther the currently logged in user is allowed to access the given controller action
     * or not. It returns true if allowed; otherwise false.
     */
    public function filterAccess($controllerName, $actionName)
    {
        if (isset($this->config[$controllerName])) {
            $items = $this->config[$controllerName];
            foreach ($items as $item) {
                $actionList = $item['actions'];
                $allow = $item['allow'];
                if (is_array($actionList) && in_array($actionName, $actionList) ||
                    $actionList=='*') {
                    if (is_string($allow)) {
                        if ($this->checkUser($allow))
                            return true;
                    } else if (is_array($allow)) {
                        foreach ($allow as $allowItem) {
                            if ($this->checkUser($allowItem)) 
                                return true;
                        }   
                    } else {
                        throw new \Exception('Expected string or array');
                    }
                }
            }            
        }
        
        return false;
    }
    
    /**
     * This method checks whether the $allow argument matches against the current
     * user. 
     * @param string|array $allow Either '*' or '@' or user email.
     * @return boolean true if user is allowed to see the page.
     */
    private function checkUser($allow)
    {
        if ($allow=='*')
            return true; // Anyone is allowed to see the page.
        else if ($allow=='@' && $this->authService->hasIdentity()) {
            return true; // Only authenticated user is allowed to see the page.
        } else if ($allow==$this->authService->getIdentity()) {
            return true; // Only user with the given email is allowed to see the page.
        }
        
        // Restrict to see this page.
        return false;
    }
}