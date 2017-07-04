<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use User\Validator\RoleExistsValidator;

/**
 * The form for collecting information about Role.
 */
class RoleForm extends Form
{
    private $scenario;
    
    private $entityManager;
    
    private $role;
    
    /**
     * Constructor.     
     */
    public function __construct($scenario='create', $entityManager = null, $role = null)
    {
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;
        $this->role = $role;
        
        // Define form name
        parent::__construct('role-form');
     
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
        // Add "name" field
        $this->add([           
            'type'  => 'text',
            'name' => 'name',
            'attributes' => [
                'id' => 'name'
            ],
            'options' => [
                'label' => 'Role Name',
            ],
        ]);
        
        // Add "description" field
        $this->add([            
            'type'  => 'textarea',
            'name' => 'description',
            'attributes' => [
                'id' => 'description'
            ],
            'options' => [
                'label' => 'Description',
            ],
        ]);
        
        // Add "inherit_roles" field
        $this->add([            
            'type'  => 'select',
            'name' => 'inherit_roles[]',
            'attributes' => [
                'id' => 'inherit_roles[]',
                'multiple' => 'multiple',
            ],
            'options' => [
                'label' => 'Optionally inherit permissions from these role(s)'
            ],
        ]);
                        
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
    
    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter() 
    {
        // Create input filter
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
        
        // Add input for "name" field
        $inputFilter->add([
                'name'     => 'name',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],                    
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 128
                        ],
                    ],
                    [
                        'name' => RoleExistsValidator::class,
                        'options' => [
                            'entityManager' => $this->entityManager,
                            'role' => $this->role
                        ],
                    ],
                ],
            ]);                          
        
        // Add input for "description" field
        $inputFilter->add([
                'name'     => 'description',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],                    
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 0,
                            'max' => 1024
                        ],
                    ],
                ],
            ]);                  
        
        // Add input for "inherit_roles" field
        $inputFilter->add([
                'name'     => 'inherit_roles[]',
                'required' => false,
                'filters'  => [
                                    
                ],                
                'validators' => [
                    
                ],
            ]);                  
    }           
}
