<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Application\Validator\PhoneValidator;

/**
 * This form is used to collect user registration data. This form is multi-step.
 * It determines which fields to create based on the $step argument you pass to
 * its constructor.
 */
class RegistrationForm extends Form
{
    /**
     * Constructor.     
     */
    public function __construct($step)
    {
        // Check input.
        if (!is_int($step) || $step<1 || $step>3)
            throw new \Exception('Step is invalid');
        
        // Define form name
        parent::__construct('registration-form');
     
        // Set POST method for this form
        $this->setAttribute('method', 'post');
                
        $this->addElements($step);
        $this->addInputFilter($step); 
    }
    
    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements($step) 
    {
        if ($step==1) {
            
            // Add "email" field
            $this->add([           
                'type'  => 'text',
                'name' => 'email',
                'attributes' => [
                    'id' => 'email'
                ],
                'options' => [
                    'label' => 'Your E-mail',
                ],
            ]);
            
            // Add "full_name" field
            $this->add([           
                'type'  => 'text',
                'name' => 'full_name',
                'attributes' => [
                    'id' => 'full_name'
                ],
                'options' => [
                    'label' => 'Full Name',
                ],
            ]);
            
            // Add "password" field
            $this->add([           
                'type'  => 'password',
                'name' => 'password',
                'attributes' => [
                    'id' => 'password'
                ],
                'options' => [
                    'label' => 'Choose Password',
                ],
            ]);
            
            // Add "confirm_password" field
            $this->add([           
                'type'  => 'password',
                'name' => 'confirm_password',
                'attributes' => [
                    'id' => 'confirm_password'
                ],
                'options' => [
                    'label' => 'Type Password Again',
                ],
            ]);           
            
        } else if ($step==2) {
            
            // Add "phone" field
            $this->add([
                'type'  => 'text',
                'name' => 'phone',
                'attributes' => [                
                    'id' => 'phone'
                ],
                'options' => [
                    'label' => 'Mobile Phone',
                ],
            ]);

            // Add "street_address" field
            $this->add([
                'type'  => 'text',
                'name' => 'street_address',
                'attributes' => [                
                    'id' => 'street_address'
                ],
                'options' => [
                    'label' => 'Street address',
                ],
            ]);
            
            // Add "city" field
            $this->add([
                'type'  => 'text',
                'name' => 'city',
                'attributes' => [                
                    'id' => 'city'
                ],
                'options' => [
                    'label' => 'City',
                ],
            ]);
            
            // Add "state" field
            $this->add([
                'type'  => 'text',
                'name' => 'state',
                'attributes' => [                
                    'id' => 'state'
                ],
                'options' => [
                    'label' => 'State',
                ],
            ]);
            
            // Add "post_code" field
            $this->add([
                'type'  => 'text',
                'name' => 'post_code',
                'attributes' => [                
                    'id' => 'post_code'
                ],
                'options' => [
                    'label' => 'Post Code',
                ],
            ]);
            
            // Add "country" field
            $this->add([            
                'type'  => 'select',
                'name' => 'country',
                'attributes' => [
                    'id' => 'country',                                
                ],                                    
                'options' => [                                
                    'label' => 'Country',
                    'empty_option' => '-- Please select --',
                    'value_options' => [
                        'US' => 'United States',
                        'CA' => 'Canada',
                        'BR' => 'Brazil',
                        'GB' => 'Great Britain',
                        'FR' => 'France',
                        'IT' => 'Italy',
                        'DE' => 'Germany',
                        'RU' => 'Russia',
                        'IN' => 'India',
                        'CN' => 'China',
                        'AU' => 'Australia',
                        'JP' => 'Japan'
                    ],                
                ],
            ]);
            
            
        } else if ($step==3) {
            
            // Add "billing_plan" field
            $this->add([            
                'type'  => 'select',
                'name' => 'billing_plan',
                'attributes' => [
                    'id' => 'billing_plan',                                
                ],                                    
                'options' => [                                
                    'label' => 'Billing Plan',
                    'empty_option' => '-- Please select --',
                    'value_options' => [
                        'Free' => 'Free',
                        'Bronze' => 'Bronze',
                        'Silver' => 'Silver',
                        'Gold' => 'Gold',
                        'Platinum' => 'Platinum'
                    ],                
                ],
            ]);
            
            // Add "payment_method" field
            $this->add([            
                'type'  => 'select',
                'name' => 'payment_method',
                'attributes' => [
                    'id' => 'payment_method',                                
                ],                                    
                'options' => [                                
                    'label' => 'Payment Method',
                    'empty_option' => '-- Please select --',
                    'value_options' => [
                        'Visa' => 'Visa',
                        'MasterCard' => 'Master Card',
                        'PayPal' => 'PayPal'
                    ],                
                ],
            ]);
        }
        
        // Add the CSRF field
        $this->add([
            'type'  => 'csrf',
            'name' => 'csrf',
            'attributes' => [],
            'options' => [                
                'csrf_options' => [
                     'timeout' => 600
                ]
            ],
        ]);
        
        // Add the submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [                
                'value' => 'Next Step',
                'id' => 'submitbutton',
            ],
        ]);        
    }
    
    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter($step) 
    {
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
        
        if ($step==1) {

            $inputFilter->add([
                    'name'     => 'email',
                    'required' => true,
                    'filters'  => [
                        ['name' => 'StringTrim'],                    
                    ],                
                    'validators' => [
                        [
                            'name' => 'EmailAddress',
                            'options' => [
                                'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                                'useMxCheck'    => false,                            
                            ],
                        ],
                    ],
                ]);
            
            $inputFilter->add([
                'name'     => 'full_name',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                    ['name' => 'StripNewlines'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 128
                        ],
                    ],
                ],
            ]);
           
            // Add input for "password" field
            $inputFilter->add([
                    'name'     => 'password',
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

            // Add input for "confirm_password" field
            $inputFilter->add([
                    'name'     => 'confirm_password',
                    'required' => true,
                    'filters'  => [
                    ],       
                    'validators' => [
                        [
                            'name'    => 'Identical',
                            'options' => [
                                'token' => 'password',                            
                            ],
                        ],
                    ],
                ]);
            
        } else if ($step==2) {
        
            $inputFilter->add([
                'name'     => 'phone',
                'required' => true,                
                'filters'  => [                    
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 32
                        ],
                    ],
                    [
                        'name' => PhoneValidator::class,
                        'options' => [
                            'format' => PhoneValidator::PHONE_FORMAT_INTL
                        ]                        
                    ],
                ],
            ]);
            
            // Add input for "street_address" field
            $inputFilter->add([
                    'name'     => 'street_address',
                    'required' => true,
                    'filters'  => [
                        ['name' => 'StringTrim'],
                    ],                
                    'validators' => [
                        ['name'=>'StringLength', 'options'=>['min'=>1, 'max'=>255]]
                    ],
                ]);

            // Add input for "city" field
            $inputFilter->add([
                    'name'     => 'city',
                    'required' => true,
                    'filters'  => [
                        ['name' => 'StringTrim'],
                    ],                
                    'validators' => [
                        ['name'=>'StringLength', 'options'=>['min'=>1, 'max'=>255]]
                    ],
                ]);

            // Add input for "state" field
            $inputFilter->add([
                    'name'     => 'state',
                    'required' => true,
                    'filters'  => [
                        ['name' => 'StringTrim'],
                    ],                
                    'validators' => [
                        ['name'=>'StringLength', 'options'=>['min'=>1, 'max'=>32]]
                    ],
                ]); 
            
            // Add input for "post_code" field
            $inputFilter->add([
                    'name'     => 'post_code',
                    'required' => true,
                    'filters'  => [                                        
                    ],                
                    'validators' => [
                        ['name' => 'IsInt'],
                        ['name'=>'Between', 'options'=>['min'=>0, 'max'=>999999]]
                    ],
                ]);

            // Add input for "country" field
            $inputFilter->add([
                    'name'     => 'country',
                    'required' => false,                
                    'filters'  => [
                        ['name' => 'Alpha'],
                        ['name' => 'StringTrim'],
                        ['name' => 'StringToUpper'],
                    ],                
                    'validators' => [
                        ['name'=>'StringLength', 'options'=>['min'=>2, 'max'=>2]]
                    ],
                ]);     
            
        } else if ($step==3) {
            
            // Add input for "billing_plan" field
            $inputFilter->add([
                    'name'     => 'billing_plan',
                    'required' => true,                
                    'filters'  => [
                    ],                
                    'validators' => [
                        [
                            'name' => 'InArray', 
                            'options' => [
                                'haystack'=>[
                                    'Free', 
                                    'Bronze',
                                    'Silver',
                                    'Gold',
                                    'Platinum'
                                ]
                            ]
                        ]
                    ],
                ]);
            
            // Add input for "payment_method" field
            $inputFilter->add([
                    'name'     => 'payment_method',
                    'required' => true,                
                    'filters'  => [
                    ],                
                    'validators' => [
                        [
                            'name' => 'InArray', 
                            'options' => [
                                'haystack'=>[
                                    'PayPal', 
                                    'Visa',
                                    'MasterCard',
                                ]
                            ]
                        ]
                    ],
                ]);
        }
    }
}


