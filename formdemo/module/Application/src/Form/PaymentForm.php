<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * This form is used to collect payment details.
 */
class PaymentForm extends Form
{
    /**
     * Constructor.     
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('payment-form');
     
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
                    'credit_card' => 'Credit Card',
                    'bank_account' => 'Bank Account',
                    'cash' => 'Cash'
                ],                
            ],
        ]);
        
        // Add "card_number" field
        $this->add([           
            'type'  => 'text',
            'name' => 'card_number',
            'attributes' => [
                'id' => 'card_number'
            ],
            'options' => [
                'label' => 'Card Number',
            ],
        ]);
       
        // Add "bank_account" field
        $this->add([           
            'type'  => 'text',
            'name' => 'bank_account',
            'attributes' => [
                'id' => 'bank_account'
            ],
            'options' => [
                'label' => 'Bank Account',
            ],
        ]);
        
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
                'value' => 'Submit',
                'id' => 'submitbutton',
            ],
        ]);        
    }
    
    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter() 
    {
        $inputFilter = $this->getInputFilter();        
        
        $inputFilter->add([
                'name'     => 'payment_method',
                'required' => true,
                'filters'  => [                    
                ],                
                'validators' => [
                    [
                        'name' => 'InArray',
                        'options' => [
                            'haystack' => ['credit_card', 'bank_account', 'cash']
                        ],
                    ],
                ],
            ]);
        
        $inputFilter->add([
                'name'     => 'card_number',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim']
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 32
                        ],
                    ],
                    [
                        'name'    => 'CreditCard',
                        'options' => [
                            'type' => [\Zend\Validator\CreditCard::VISA]
                        ],
                    ],
                ],
            ]);
        
        $inputFilter->add([
                'name'     => 'bank_account',
                'required' => true,
                'filters'  => [                    
                    ['name' => 'StringTrim'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 20,
                            'max' => 20
                        ],
                    ],
                ],
            ]);
    }
}

