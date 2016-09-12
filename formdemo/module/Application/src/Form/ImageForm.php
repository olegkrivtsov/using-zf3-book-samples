<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\FileInput;

/**
 * The ImageForm form model is used for uploading an image file.
 */
class ImageForm extends Form
{
    /**
     * Constructor.     
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('image-form');
     
        // Set POST method for this form
        $this->setAttribute('method', 'post');
        
        // Set binary content encoding
        $this->setAttribute('enctype', 'multipart/form-data');  
        
        $this->addElements();
        $this->addInputFilter();          
    }
    
    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements() 
    {
        // Add "file" field
        $this->add([
            'type'  => 'file',
            'name' => 'file',
            'attributes' => [               
                'id' => 'file'
            ],
            'options' => [
                'label' => 'Image file',
            ],
        ]);
        
        // Add the submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [                
                'value' => 'Upload',
                'id' => 'submitbutton',
            ],
        ]);       
        
    }
    
    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter() 
    {
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
        
        // Add validation rules for the "file" field	 
        $inputFilter->add([
                'type'     => FileInput::class,
                'name'     => 'file',
                'required' => true,                           
                'validators' => [
                    ['name'    => 'FileUploadFile'],
                    ['name'    => 'FileIsImage'],                          
                    [
                        'name'    => 'FileImageSize',                        
                        'options' => [                            
                            'minWidth'  => 128,
                            'minHeight' => 128,
                            'maxWidth'  => 4096,
                            'maxHeight' => 4096
                        ]
                    ],
                ],
                'filters'  => [                    
                    [
                        'name' => 'FileRenameUpload',
                        'options' => [  
                            'target'=>'./data/upload',
                            'useUploadName'=>true,
                            'useUploadExtension'=>true,
                            'overwrite'=>true,
                            'randomize'=>false
                        ]
                    ]
                ],     
            ]);                        
    }
}
