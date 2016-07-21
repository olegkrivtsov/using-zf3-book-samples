<?php
namespace Application\Service;

use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;

/**
 * This service class is used to deliver an E-mail message to recipient.
 */
class MailSender 
{
    /**
     * Sends the mail message.
     */
    public function sendMail($sender, $recipient, $subject, $text) 
    {
        $result = false;
        try {        
            // Create E-mail message
            $mail = new Message();
            $mail->setBody($text);
            $mail->setFrom($sender);
            $mail->addTo($recipient);
            $mail->setSubject($subject);

            // Send E-mail message
            $transport = new Sendmail('-f'.$sender);
            $transport->send($mail);
            $result = true;
        } catch(\Exception $e) {
            $result = false;
        }
        
        return $result;
    }
};


