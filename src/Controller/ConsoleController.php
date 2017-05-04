<?php
/**
 * Created by PhpStorm.
 * User: Developer
 * Date: 5/4/2017
 * Time: 1:02 PM
 */

namespace ProspectOne\UserModule\Controller;


use ProspectOne\UserModule\Entity\User;
use ProspectOne\UserModule\Service\UserManager;
use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;

/**
 * Class ConsoleController
 * @package ProspectOne\UserModule\Controller
 */
class ConsoleController extends AbstractActionController
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * Entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @return UserManager
     */
    public function getUserManager(): UserManager
    {
        return $this->userManager;
    }

    /**
     * @param UserManager $userManager
     * @return ConsoleController
     */
    public function setUserManager(UserManager $userManager): ConsoleController
    {
        $this->userManager = $userManager;
        return $this;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     * @return ConsoleController
     */
    public function setEntityManager(EntityManager $entityManager): ConsoleController
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * ConsoleController constructor.
     * @param UserManager $userManager
     * @param EntityManager $entityManager
     */
    public function __construct(UserManager $userManager, EntityManager $entityManager)
    {
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
    }

    /**
     *
     */
    public function regenerateTokensAction()
    {
        /** @var User[] $users */
        $users = $this->entityManager->getRepository(User::class)->findAll();
        foreach($users as $user) {
            $token = $this->getUserManager()->generateToken();
            $user->setToken($token);
            $this->getEntityManager()->persist($user);
        }
        $this->getEntityManager()->flush();
    }
}
