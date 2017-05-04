<?php
namespace ProspectOne\UserModule\Service;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\Crypt\Password\Bcrypt;
use ProspectOne\UserModule\Entity\User;

/**
 * Adapter used for authenticating user. It takes login and password on input
 * and checks the database if there is a user with such login (email) and password.
 * If such user exists, the service returns its identity (email). The identity
 * is saved to session and can be retrieved later with Identity view helper provided
 * by ZF3.
 */
class AuthAdapter implements AdapterInterface
{
    /**
     * User email.
     * @var string 
     */
    private $email;
    
    /**
     * Password
     * @var string 
     */
    private $password;
    
    /**
     * Entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var Bcrypt
     */
    private $bcrypt;

    /**
     * @var bool
     */
    private $headerAuthEnabled;

    /**
     * @var string
     */
    private $authHeader;

    /**
     * @return bool
     */
    protected function isHeaderAuthEnabled(): bool
    {
        return $this->headerAuthEnabled;
    }

    /**
     * @param bool $headerAuthEnabled
     * @return AuthAdapter
     */
    protected function setHeaderAuthEnabled(bool $headerAuthEnabled): AuthAdapter
    {
        $this->headerAuthEnabled = $headerAuthEnabled;
        return $this;
    }

    /**
     * @return string
     */
    protected function getAuthHeader(): ?string
    {
        return $this->authHeader;
    }

    /**
     * @param string $authHeader
     * @return AuthAdapter
     */
    protected function setAuthHeader(string $authHeader): AuthAdapter
    {
        $this->authHeader = $authHeader;
        return $this;
    }

    /**
     * AuthAdapter constructor.
     * @param $entityManager
     * @param Bcrypt $bcrypt
     * @param bool $headerAuthEnabled
     * @param string $headerValue
     */
    public function __construct($entityManager, Bcrypt $bcrypt, bool $headerAuthEnabled, ?string $headerValue)
    {
        $this->entityManager = $entityManager;
        $this->bcrypt = $bcrypt;
        $this->headerAuthEnabled = $headerAuthEnabled;
        $this->authHeader = $headerValue;
    }
    
    /**
     * Sets user email.     
     */
    public function setEmail($email) 
    {
        $this->email = $email;        
    }
    
    /**
     * Sets password.     
     */
    public function setPassword($password) 
    {
        $this->password = (string)$password;        
    }
    
    /**
     * Performs an authentication attempt.
     */
    public function authenticate()
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($this->email);
        return $this->validateUser($user);


    }

    public function headerAuth()
    {
        if (!$this->isHeaderAuthEnabled()) {
            return false;
        }

        if (empty($this->getAuthHeader())) {
            return false;
        }

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)
            ->findOneByToken($this->getAuthHeader());
        return $this->validateUser($user);
    }

    /**
     * @param $user
     * @return Result
     */
    private function validateUser(?User $user): Result
    {
// If there is no such user, return 'Identity Not Found' status.
        if ($user == null) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                ['Invalid credentials.']);
        }

        // If the user with such email exists, we need to check if it is active or retired.
        // Do not allow retired users to log in.
        if ($user->getStatus() == User::STATUS_RETIRED) {
            return new Result(
                Result::FAILURE,
                null,
                ['User is retired.']);
        }

        $passwordHash = $user->getPassword();

        if ($this->bcrypt->verify($this->password, $passwordHash)) {
            // Great! The password hash matches. Return user identity (email) to be
            // saved in session for later use.
            return new Result(
                Result::SUCCESS,
                $this->email,
                ['Authenticated successfully.']);
        }

        // If password check didn't pass return 'Invalid Credential' failure status.
        return new Result(
            Result::FAILURE_CREDENTIAL_INVALID,
            null,
            ['Invalid credentials.']);
    }
}


