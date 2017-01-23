<?php

namespace ProspectOne\UserModule\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_role")
 */
class Role
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $roleId;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true, nullable=true, name="name")
     */
    protected $roleName;

    /**
     * Get the role id
     * @return int
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * Set the role id
     * @param int $roleId
     * @return void
     */
    public function setRoleId($roleId)
    {
        $this->roleId = (int) $roleId;
    }

    /**
     * Get the role name
     * @return string
     */
    public function getRoleName()
    {
        return $this->roleName;
    }

    /**
     * Set the role name
     * @param string $roleName
     * @return void
     */
    public function setRoleName($roleName)
    {
        $this->roleName = $roleName;
    }
}
