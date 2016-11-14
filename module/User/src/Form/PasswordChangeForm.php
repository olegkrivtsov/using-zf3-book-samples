<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;

/**
 * This form is used when changing user's password (to collect user's old password 
 * and new password) or when resetting user's password (when user forgot his password).
 */
class PasswordChangeForm extends Form
{   
    // There can be two scenarios - 'change' or 'reset'.
    private $scenario;
    
    /**
     * Constructor.
     * @param string $scenario Either 'change' or 'reset'.     
     */
    public function __construct($scenario)
    {
        // Define form name
        parent::__construct('password-change-form');
     
        $this->scenario = $scenario;
        
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
        // If scenario is 'change', we do not ask for old password.
        if ($this->scenario == 'change') {
        
            // Add "old_password" field
            $this->add([            
                'type'  => 'password',
                'name' => 'old_password',
                'options' => [
                    'label' => 'Old Password',
                ],
            ]);       
        }
        
        // Add "new_password" field
        $this->add([            
            'type'  => 'password',
            'name' => 'new_password',
            'options' => [
                'label' => 'New Password',
            ],
        ]);
        
        // Add "confirm_new_password" field
        $this->add([            
            'type'  => 'password',
            'name' => 'confirm_new_password',
            'options' => [
                'label' => 'Confirm new password',
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
        
        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [                
                'value' => 'Change Password'
            ],
        ]);
    }
    
    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter() 
    {
        // Create main input filter
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
        
        if ($this->scenario == 'change') {
            
            // Add input for "old_password" field
            $inputFilter->add([
                    'name'     => 'old_password',
                    'required' => true,
                    'filters'  => [                    
                    ],                
                    'validators' => [
                        [
                            'name'    => 'StringLength',
                            'options' => [
                                'min' => 6,
                                'max' => 64
                            ],
                        ],
                    ],
                ]);      
        }
        
        // Add input for "new_password" field
        $inputFilter->add([
                'name'     => 'new_password',
                'required' => true,
                'filters'  => [                    
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 6,
                            'max' => 64
                        ],
                    ],
                ],
            ]);
        
        // Add input for "confirm_new_password" field
        $inputFilter->add([
                'name'     => 'confirm_new_password',
                'required' => true,
                'filters'  => [                    
                ],                
                'validators' => [
                    [
                        'name'    => 'Identical',
                        'options' => [
                            'token' => 'new_password',                            
                        ],
                    ],
                ],
            ]);
    }
}

