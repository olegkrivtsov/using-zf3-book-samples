<?php
namespace ProspectOne\UserModule\Entity;

use Doctrine\ORM\Mapping as ORM;
use ProspectOne\UserModule\Interfaces\UserInterface;

/**
 * This class represents a registered user.
 * Adds role system
 * @ORM\Entity()
 * @ORM\Table(name="user",uniqueConstraints={@ORM\UniqueConstraint(name="token_idx", columns={"token"}),@ORM\UniqueConstraint(name="email_idx", columns={"email"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 */
class User implements UserInterface
{
    // User status constants.
    const STATUS_ACTIVE = 1; // Active user.
    const STATUS_RETIRED = 2; // Retired user.

    /**
     * @var Role
     * @ORM\ManyToOne(targetEntity="ProspectOne\UserModule\Entity\Role", fetch="EAGER")
     * @ORM\JoinColumn(name="r_user_role")
     */
    protected $role;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="email")
     */
    protected $email;

    /**
     * @ORM\Column(name="full_name")
     */
    protected $fullName;

    /**
     * @ORM\Column(name="password")
     */
    protected $password;

    /**
     * @ORM\Column(name="status")
     */
    protected $status;

    /**
     * @ORM\Column(name="date_created")
     */
    protected $dateCreated;

    /**
     * @ORM\Column(name="pwd_reset_token", nullable=true)
     */
    protected $passwordResetToken;

    /**
     * @ORM\Column(name="pwd_reset_token_creation_date", nullable=true)
     */
    protected $passwordResetTokenCreationDate;

    /**
     * @var string
     * @ORM\Column(name="token", nullable=true)
     */
    protected $token;

    /**
     * Get role.
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Get Role Name.
     * @return string
     */
    public function getRoleName()
    {
        if(!empty($this->role)) {
            return $this->role->getRoleName();
        } else {
            return 'N/A';
        }
    }
    /**
     * Add a role to the user.
     * @param Role $role
     * @return User
     */
    public function addRole($role) : User
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Returns user ID.
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets user ID.
     * @param int $id
     * @return User
     */
    public function setId($id) : User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns email.
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets email.
     * @param string $email
     * @return User
     */
    public function setEmail($email) : User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Returns full name.
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Sets full name.
     * @param string $fullName
     * @return user
     */
    public function setFullName($fullName) : User
    {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * Returns status.
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns possible statuses as array.
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_RETIRED => 'Retired'
        ];
    }

    /**
     * Returns user status as string.
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getStatusList();
        if (isset($list[$this->status]))
            return $list[$this->status];

        return 'Unknown';
    }

    /**
     * Sets status.
     * @param int $status
     * @return User
     */
    public function setStatus($status) : User
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Returns password.
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets password.
     * @param string $password
     * @return User
     */
    public function setPassword($password) : User
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Returns the date of user creation.
     * @return string
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Sets the date when this user was created.
     * @param string $dateCreated
     * @return User
     */
    public function setDateCreated($dateCreated) : User
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * Returns password reset token.
     * @return string
     */
    public function getResetPasswordToken()
    {
        return $this->passwordResetToken;
    }

    /**
     * Sets password reset token.
     * @param string $token
     * @return User
     */
    public function setPasswordResetToken($token) : User
    {
        $this->passwordResetToken = $token;
        return $this;
    }

    /**
     * Returns password reset token's creation date.
     * @return string
     */
    public function getPasswordResetTokenCreationDate()
    {
        return $this->passwordResetTokenCreationDate;
    }

    /**
     * Sets password reset token's creation date.
     * @param string $date
     * @return $this
     */
    public function setPasswordResetTokenCreationDate($date) : User
    {
        $this->passwordResetTokenCreationDate = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return User
     */
    public function setToken(?string $token): User
    {
        $this->token = $token;
        return $this;
    }
}
