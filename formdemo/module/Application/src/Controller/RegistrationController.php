<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\RegistrationForm;
use Zend\Session\Container;

/**
 * This is the controller class displaying a page with the User Registration form.
 * User registration has several steps, so we display different form elements on
 * each step. We use session container to remember user's choices on the previous
 * steps.
 */
class RegistrationController extends AbstractActionController 
{
    /**
     * Session container.
     * @var Zend\Session\Container
     */
    private $sessionContainer;
    
    /**
     * Constructor. Its goal is to inject dependencies into controller.
     */
    public function __construct($sessionContainer) 
    {
        $this->sessionContainer = $sessionContainer;
    }
    
    /**
     * This is the default "index" action of the controller. It displays the 
     * User Registration page.
     */
    public function indexAction() 
    {
        // Determine the current step.
        $step = 1;
        if (isset($this->sessionContainer->step)) {
            $step = $this->sessionContainer->step;            
        }
        
        // Ensure the step is correct (between 1 and 3).
        if ($step<1 || $step>3)
            $step = 1;
        
        if ($step==1) {
            // Init user choices.
            $this->sessionContainer->userChoices = [];
        }
                       
        $form = new RegistrationForm($step);
        
        // Check if user has submitted the form
        if($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Save user choices in session.
                $this->sessionContainer->userChoices["step$step"] = $data;
                
                // Increase step
                $step ++;
                $this->sessionContainer->step = $step;
                
                // If we completed all 3 steps, redirect to Review page.
                if ($step>3) {
                    return $this->redirect()->toRoute('registration', ['action'=>'review']);
                }
                
                // Go to the next step.
                return $this->redirect()->toRoute('registration');
            }
        }
        
        $viewModel = new ViewModel([
            'form' => $form
        ]);
        $viewModel->setTemplate("application/registration/step$step");
        
        return $viewModel;
    }
    
    /**
     * The "review" action shows a page allowing to review data entered on previous
     * three steps.
     */
    public function reviewAction()
    {
        // Validate session data.
        if(!isset($this->sessionContainer->step) || 
           $this->sessionContainer->step<=3 || 
           !isset($this->sessionContainer->userChoices)) {
            throw new \Exception('Sorry, the data is not available for review yet');
        }
        
        // Retrieve user choices from session.
        $userChoices = $this->sessionContainer->userChoices;
        
        return new ViewModel([
            'userChoices' => $userChoices
        ]);
    }
}

