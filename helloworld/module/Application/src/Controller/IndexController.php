<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Barcode\Barcode;
use Zend\Mvc\MvcEvent;

/**
 * This is the main controller class of the Hello World application. The 
 * controller class is used to receive user input, validate user input, 
 * pass the data to the models and pass the results 
 * returned by models to the view for rendering.
 */
class IndexController extends AbstractActionController 
{
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
        $appName = 'Hello World';
        $appDescription = 'Hello World sample application for Using Zend Framework 3 book';
        
        // Return variables to view script with the help of
        // ViewObject variable container
        return new ViewModel([
            'appName' => $appName,
            'appDescription' => $appDescription
        ]);
    }
       
    /**
     * This is the 'download' action that is invoked
     * when a user wants to download the given file.     
     */
    public function downloadAction() 
    {
        // Get the file name from GET variable
        $fileName = (string)$this->params()->fromQuery('file', '');
        
        trim($fileName);
        if (strlen($fileName)==0) {
            // Set 404 Not Found status code
            $this->getResponse()->setStatusCode(404);            
            return;
        }
        
        // Take some precautions to Make file name secure
        str_replace("/", "", $fileName);  // Remove slashes
        str_replace("\\", "", $fileName); // Remove back-slashes
        
        // Try to open file
        $path = './data/download/' . $fileName;
        if (!is_readable($path)) {
            // Set 404 Not Found status code
            $this->getResponse()->setStatusCode(404);            
            return;
        }
            
        // Get file size in bytes
        $fileSize = filesize($path);

        // Write HTTP headers
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine("Content-type: application/octet-stream");
        $headers->addHeaderLine("Content-Disposition: attachment; filename=\"" . $fileName . "\"");
        $headers->addHeaderLine("Content-length: $fileSize");
        $headers->addHeaderLine("Cache-control: private"); //use this to open files directly
            
        // Write file content        
        $fileContent = file_get_contents($path);
        if ($fileContent!=false) {                
            $response->setContent($fileContent);
        } else {        
            // Set 500 Server Error status code
            $this->getResponse()->setStatusCode(500);
            return;
        }
        
        // Return Response to avoid default view rendering
        return $this->getResponse();
    }

    /**
     * This is the "doc" action which displays a "static" documentation page.
     */
    public function docAction() 
    {
        $pageTemplate = 'application/index/doc' . $this->params()->fromRoute('page', 'documentation.phtml');        
        $filePath = __DIR__ . '/../../view/' . $pageTemplate . '.phtml';
        if (!file_exists($filePath) || !is_readable($filePath)) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $viewModel = new ViewModel([
            'page'=>$pageTemplate
        ]);
        $viewModel->setTemplate($pageTemplate);
        
        return $viewModel;
    }
    
    /**
     * This is the "static" action which displays a static documentation page.
     */
    public function staticAction() 
    {
        // Get path to view template from route params
        $pageTemplate = $this->params()->fromRoute('page', null);
        if ($pageTemplate==null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Render the page
        $viewModel = new ViewModel([
            'page'=>$pageTemplate
        ]);
        $viewModel->setTemplate($pageTemplate);
        return $viewModel;
    }
    
    /**
     * This is the "barcode" action. It generate the HELLO-WORLD barcode image.     
     */
    public function barcodeAction() 
    {
        // Get parameters from route.
        $type = $this->params()->fromRoute('type', 'code39');
        $label = $this->params()->fromRoute('label', 'HELLO-WORLD');

        // Set barcode options.
        $barcodeOptions = ['text' => $label];        
        $rendererOptions = [];
        
        // Create barcode object
        $barcode = Barcode::factory(
                $type, 'image', $barcodeOptions, $rendererOptions
                );
        
        // The line below will output barcode image to standard output stream.
        $barcode->render();

        // Return false to disable default view rendering. 
        return false;
    }  
    
    /**
     * An action that demonstrates the usage of partial views.
     */
    public function partialDemoAction() 
    {
        $products = [
            [
                'id' => 1,
                'name' => 'Digital Camera',
                'price' => 99.95,
            ],
            [
                'id' => 2,
                'name' => 'Tripod',
                'price' => 29.95,
            ],
            [
                'id' => 3,
                'name' => 'Camera Case',
                'price' => 2.99,
            ],
            [
                'id' => 4,
                'name' => 'Batteries',
                'price' => 39.99,
            ],
            [
                'id' => 5,
                'name' => 'Charger',
                'price' => 29.99,
            ],
        ];
        
        return new ViewModel([
                'products' => $products
            ]);
    }
    
    /**
     * This action demonstrates how to return JSON response.
     */ 
    public function getJsonAction()
    {
        return new JsonModel([
            'status' => 'SUCCESS',
            'message'=>'Here is your data',
            'data' => [
                'full_name' => 'John Doe',
                'address' => '51 Middle st.'
            ]
        ]);
    }
    
    /** 
     * We override the parent class' onDispatch() method to
     * set an alternative layout for all actions in this controller.
     */
    public function onDispatch(MvcEvent $e) 
    {
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);        
        
        // Set alternative layout
        $this->layout()->setTemplate('/layout/layout2');                
        
        // Return the response
        return $response;
    }    
}
