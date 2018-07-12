<?php
namespace User\Repository;

use Doctrine\ORM\EntityRepository;
use User\Entity\User;

/**
 * This is the custom repository class for User entity.
 */
class UserRepository extends EntityRepository
{
    /**
     * Retrieves all users in descending dateCreated order.
     * @return Query
     */
    public function findAllUsers()
    {
        $entityManager = $this->getEntityManager();
        
        $queryBuilder = $entityManager->createQueryBuilder();
        
        $queryBuilder->select('u')
            ->from(User::class, 'u')
            ->orderBy('u.dateCreated', 'DESC');
        
        return $queryBuilder->getQuery();
    }
}