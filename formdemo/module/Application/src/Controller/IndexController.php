<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\ContactForm;
use Application\Service\MailSender;

/**
 * This is the main controller class of the Form Demo application. The 
 * controller class is used to receive user input, 
 * pass the data to the models and pass the results returned by models to the 
 * view for rendering.
 */
class IndexController extends AbstractActionController 
{
    /**
     * Mail sender.
     * @var Application\Service\MailSender
     */
    private $mailSender;
    
    /**
     * Constructor.
     */
    public function __construct($mailSender) 
    {
        $this->mailSender = $mailSender;
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
     * This action displays the About page.
     */
    public function aboutAction() 
    {   
        $appName = 'FormDemo';
        $appDescription = 'Form demo for the Using Zend Framework 3 book';
        
        return new ViewModel([
            'appName' => $appName,
            'appDescription' => $appDescription
        ]);
    }
    
    /**
     * This action displays the Contact Us page.
     */
    public function contactUsAction() 
    {   
        // Create Contact Us form
        $form = new ContactForm();
        
        // Check if user has submitted the form
        if($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                $email = $data['email'];
                $subject = $data['subject'];
                $body = $data['body'];
                
                // Send E-mail
                if(!$this->mailSender->sendMail('no-reply@example.com', $email, 
                        $subject, $body)) {
                    // In case of error, redirect to "Error Sending Email" page
                    return $this->redirect()->toRoute('application', 
                            ['action'=>'sendError']);
                }
                
                // Redirect to "Thank You" page
                return $this->redirect()->toRoute('application', 
                        ['action'=>'thankYou']);
            }               
        } 
        
        // Pass form variable to view
        return new ViewModel([
            'form' => $form
        ]);
    }
    
    /**
     * This action displays the Thank You page. The user is redirected to this
     * page on successful mail delivery.
     */
    public function thankYouAction() 
    {
        return new ViewModel();
    }
    
    /**
     * This action displays the Send Error page. The user is redirected to this
     * page on mail delivery error.
     */
    public function sendErrorAction() 
    {
        return new ViewModel();
    }
}
