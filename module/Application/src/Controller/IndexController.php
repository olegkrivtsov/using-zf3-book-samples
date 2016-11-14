<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Barcode\Barcode;
use Zend\Mvc\MvcEvent;
use User\Entity\User;

/**
 * This is the main controller class of the User Demo application. It contains
 * site-wide actions such as Home or About.
 */
class IndexController extends AbstractActionController 
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /**
     * Constructor. Its purpose is to inject dependencies into the controller.
     */
    public function __construct($entityManager) 
    {
       $this->entityManager = $entityManager;
    }
    
    /**
     * This is the default "index" action of the controller. It displays the 
     * Home page.
     */
    public function indexAction() 
    {
        return new ViewModel();
    }

    /**
     * This is the "about" action. It is used to display the "About" page.
     */
    public function aboutAction() 
    {              
        $appName = 'User Demo';
        $appDescription = 'This demo shows how to implement user management with Zend Framework 3';
        
        // Return variables to view script with the help of
        // ViewObject variable container
        return new ViewModel([
            'appName' => $appName,
            'appDescription' => $appDescription
        ]);
    }  
    
    /**
     * The "settings" action displays the info about currently logged in user.
     */
    public function settingsAction()
    {
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($this->identity());
        
        if ($user==null) {
            throw new \Exception('Not found user with such email');
        }
        
        return new ViewModel([
            'user' => $user
        ]);
    }
}

