<?php
namespace ProspectOne\UserModule\Service;

use ProspectOne\UserModule\Entity\Role;
use ProspectOne\UserModule\Entity\User;
use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class UserManager
{
    const ADMIN_ROLE_ID = 2;
    const ADMIN_EMAIL = 'admin@example.com';
    const ADMIN_NAME = 'Admin';
    const ADMIN_PASSWORD = 'Secur1ty';
    const TOKEN_SIZE = 128;

    /**
     * Doctrine entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var Bcrypt
     */
    private $bcrypt;
    
    /**
     * UserManager constructor.
     * @param $entityManager
     * @param Bcrypt $bcrypt
     */
    public function __construct($entityManager, Bcrypt $bcrypt)
    {
        $this->entityManager = $entityManager;
        $this->bcrypt = $bcrypt;
    }
    
    /**
     * This method adds a new user.
     */
    public function addUser($data) 
    {
        // Do not allow several users with the same email address.
        if($this->checkUserExists($data['email'])) {
            throw new \Exception("User with email address " . $data['$email'] . " already exists");
        }
        
        // Create new User entity.
        $user = new User();
        $user->setEmail($data['email']);
        $user->setFullName($data['full_name']);

        // Get role object based on role Id from form
        /** @var Role $role */
        $role = $this->entityManager->find(Role::class, $data['role']);
        // Set role to user
        $user->addRole($role);

        // Encrypt password and store the password in encrypted state.
        $passwordHash = $this->bcrypt->create($data['password']);
        $user->setPassword($passwordHash);
        
        $user->setStatus($data['status']);
        
        $currentDate = date('Y-m-d H:i:s');
        $user->setDateCreated($currentDate);        

        // Add the entity to the entity manager.
        $this->entityManager->persist($user);
        
        // Apply changes to database.
        $this->entityManager->flush();
        
        return $user;
    }

    /**
     * This method updates data of an existing user.
     * @param User $user
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function updateUser(User $user, $data)
    {
        // Do not allow to change user email if another user with such email already exits.
        if($user->getEmail()!=$data['email'] && $this->checkUserExists($data['email'])) {
            throw new \Exception("Another user with email address " . $data['email'] . " already exists");
        }
        
        $user->setEmail($data['email']);
        $user->setFullName($data['full_name']);        
        $user->setStatus($data['status']);

        // Get role object based on role Id from form
        /** @var Role $role */
        $role = $this->entityManager->find(Role::class, $data['role']);
        // Set role to user
        $user->addRole($role);
        
        // Apply changes to database.
        $this->entityManager->flush();

        return true;
    }
    
    /**
     * This method checks if at least one user presents, and if not, creates 
     * 'Admin' user with email 'admin@example.com' and password 'Secur1ty'. 
     */
    public function createAdminUserIfNotExists()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([]);
        if ($user==null) {
            $user = new User();
            $user->setEmail(self::ADMIN_EMAIL);
            $user->setFullName(self::ADMIN_NAME);
            $passwordHash = $this->bcrypt->create(self::ADMIN_PASSWORD);
            $user->setPassword($passwordHash);
            $user->setStatus(User::STATUS_ACTIVE);
            $user->setDateCreated(date('Y-m-d H:i:s'));
            // Get role object based on role Id from form
            /** @var Role $role */
            $role = $this->entityManager->find(Role::class, self::ADMIN_ROLE_ID);
            // Set role to user
            $user->addRole($role);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    /**
     * @param string $email
     * @param string[] $roles
     * @return bool
     */
    public function hasRole($email, $roles)
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneByEmail($email);
        if(in_array($user->getRoleName(),$roles, true)) {
            return true;
        }
        return false;
    }

    /**
     * Checks whether an active user with given email address already exists in the database.     
     */
    public function checkUserExists($email) {
        
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($email);
        
        return $user !== null;
    }

    /**
     * Checks that the given password is correct.
     * @param User $user
     * @param $password
     * @return bool
     */
    public function validatePassword(User $user, $password)
    {
        $passwordHash = $user->getPassword();
        
        if ($this->bcrypt->verify($password, $passwordHash)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Generates a password reset token for the user. This token is then stored in database and 
     * sent to the user's E-mail address. When the user clicks the link in E-mail message, he is 
     * directed to the Set Password page.
     * @param User $user
     */
    public function generatePasswordResetToken(User $user)
    {
        // Generate a token.
        $token = Rand::getString(32, '0123456789abcdefghijklmnopqrstuvwxyz');
        $user->setPasswordResetToken($token);
        
        $currentDate = date('Y-m-d H:i:s');
        $user->setPasswordResetTokenCreationDate($currentDate);  
        
        $this->entityManager->flush();
        
        $subject = 'Password Reset';
            
        $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
        $passwordResetUrl = 'http://' . $httpHost . '/set-password/' . $token;
        
        $body = 'Please follow the link below to reset your password:\n';
        $body .= "$passwordResetUrl\n";
        $body .= "If you haven't asked to reset your password, please ignore this message.\n";
        
        // Send email to user.
        mail($user->getEmail(), $subject, $body);
    }
    
    /**
     * Checks whether the given password reset token is a valid one.
     */
    public function validatePasswordResetToken($passwordResetToken)
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByPasswordResetToken($passwordResetToken);
        
        if($user==null) {
            return false;
        }
        
        $tokenCreationDate = $user->getPasswordResetTokenCreationDate();
        $tokenCreationDate = strtotime($tokenCreationDate);
        
        $currentDate = strtotime('now');
        
        if ($currentDate - $tokenCreationDate > 24*60*60) {
            return false; // expired
        }
        
        return true;
    }
    
    /**
     * This method sets new password by password reset token.
     */
    public function setNewPasswordByToken($passwordResetToken, $newPassword)
    {
        if (!$this->validatePasswordResetToken($passwordResetToken)) {
           return false; 
        }
        
        $user = $this->entityManager->getRepository(User::class)
                ->findOneBy(['passwordResetToken'=>$passwordResetToken]);
        
        if ($user===null) {
            return false;
        }
                
        // Set new password for user
        $passwordHash = $this->bcrypt->create($newPassword);
        $user->setPassword($passwordHash);
                
        // Remove password reset token
        $user->setPasswordResetToken(null);
        $user->setPasswordResetTokenCreationDate(null);
        
        $this->entityManager->flush();
        
        return true;
    }
    
    /**
     * This method is used to change the password for the given user. To change the password,
     * one must know the old password.
     *
     * @param User $user
     * @param $data
     * @return bool
     */
    public function changePassword(User $user, $data)
    {
        $oldPassword = $data['old_password'];
        
        // Check that old password is correct
        if (!$this->validatePassword($user, $oldPassword)) {
            return false;
        }                
        
        $newPassword = $data['new_password'];
        
        // Check password length
        if (strlen($newPassword)<6 || strlen($newPassword)>64) {
            return false;
        }
        
        // Set new password for user
        $passwordHash = $this->bcrypt->create($newPassword);
        $user->setPassword($passwordHash);
        
        // Apply changes
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param User $user
     * @param string $token
     */
    public function updateToken(User $user, string $token)
    {
        $user->setToken($token);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @return string
     */
    public function generateToken()
    {
        $token = bin2hex(random_bytes(self::TOKEN_SIZE));
        return $token;
    }
}

