<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\ImageForm;

/**
 * This controller is designed for managing image file uploads.
 */
class ImageController extends AbstractActionController 
{
    /**
     * Image manager.
     * @var Application\Service\ImageManager;
     */
    private $imageManager;
    
    /**
     * Constructor.
     */
    public function __construct($imageManager)
    {
        $this->imageManager = $imageManager;
    }
    
    /**
     * This is the default "index" action of the controller. It displays the 
     * Image Gallery page which contains the list of uploaded images.
     */
    public function indexAction() 
    {
        // Get the list of already saved files.
        $files = $this->imageManager->getSavedFiles();
        
        // Render the view template
        return new ViewModel([
            'files'=>$files
        ]);
    }
    
    /**
     * This action shows the image upload form. This page allows to upload 
     * a single file.
     */
    public function uploadAction() 
    {
        // Create the form model
        $form = new ImageForm();
        
        // Check if user has submitted the form
        if($this->getRequest()->isPost()) {
            
            // Make certain to merge the files info!
            $request = $this->getRequest();
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            
            // Pass data to form
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Move uploaded file to its destination directory.
                $data = $form->getData();
                
                // Redirect the user to "Image Gallery" page
                return $this->redirect()->toRoute('images', ['action'=>'index']);
            }                        
        } 
        
        // Render the page
        return new ViewModel([
            'form' => $form
        ]);
    }
    
    /**
     * This is the 'file' action that is invoked when a user wants to 
     * open the image file in a web browser or generate a thumbnail.        
     */
    public function fileAction() 
    {
        // Get the file name from GET variable
        $fileName = $this->params()->fromQuery('name', '');
                
        // Check whether the user needs a thumbnail or a full-size image
        $isThumbnail = (bool)$this->params()->fromQuery('thumbnail', false);
        
        // Validate input parameters
        if (empty($fileName) || strlen($fileName)>128) {
            throw new \Exception('File name is empty or too long');
        }
        
        // Get path to image file
        $fileName = $this->imageManager->getImagePathByName($fileName);
                
        if($isThumbnail) {        
            // Resize the image
            $fileName = $this->imageManager->resizeImage($fileName);
        }
                
        // Get image file info (size and MIME type).
        $fileInfo = $this->imageManager->getImageFileInfo($fileName);        
        if ($fileInfo===false) {
            // Set 404 Not Found status code
            $this->getResponse()->setStatusCode(404);            
            return;
        }
                
        // Write HTTP headers.
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine("Content-type: " . $fileInfo['type']);        
        $headers->addHeaderLine("Content-length: " . $fileInfo['size']);
            
        // Write file content        
        $fileContent = $this->imageManager->getImageFileContent($fileName);
        if($fileContent!==false) {                
            $response->setContent($fileContent);
        } else {        
            // Set 500 Server Error status code
            $this->getResponse()->setStatusCode(500);
            return;
        }
        
        if($isThumbnail) {
            // Remove temporary thumbnail image file.
            unlink($fileName);
        }
        
        // Return Response to avoid default view rendering.
        return $this->getResponse();
    }    
}


