<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\ArrayInput;
use User\Validator\PermissionExistsValidator;

/**
 * The form for collecting information about permissions assigned to a role.
 */
class RolePermissionsForm extends Form
{
    private $entityManager;
    
    /**
     * Constructor.     
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
        
        // Define form name
        parent::__construct('role-permissions-form');
     
        // Set POST method for this form
        $this->setAttribute('method', 'post');
        
        $this->addElements();
        $this->addInputFilter();          
    }
    
    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements() 
    {
        // Add a fieldset for permissions
        $fieldset = new Fieldset('permissions');
        $this->add($fieldset);
        
        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [                
                'value' => 'Create',
                'id' => 'submit',
            ],
        ]);
        
        // Add the CSRF field
        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                'timeout' => 600
                ]
            ],
        ]);
    }
    
    public function addPermissionField($name, $label, $isDisabled = false)
    {
        // Add a permission field
        $this->get('permissions')->add([            
            'type'  => 'checkbox',
            'name' => $name,
            'attributes' => [
                'id' => $name,
                'disabled' => $isDisabled
            ],
            'options' => [
                'label' => $label
            ],
        ]);
        
        // Add input 
        $this->getInputFilter()->get('permissions')->add([
                'name'     => $name,
                'required' => false,
                'filters'  => [                    
                ],                
                'validators' => [
                    ['name' => 'IsInt'],
                ],
            ]);  
    }
    
    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter() 
    {
        // Create input filter
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
        
             
    }           
}
