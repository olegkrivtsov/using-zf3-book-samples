<?php
namespace User\View\Helper;

use Zend\View\Helper\AbstractHelper;
use User\Entity\User;

/**
 * This view helper is used for retrieving the User entity of currently logged in user.
 */
class CurrentUser extends AbstractHelper 
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /**
     * Authentication service.
     * @var Zend\Authentication\AuthenticationService
     */
    private $authService;
    
    /**
     * Previously fetched User entity.
     * @var User\Entity\User
     */
    private $user = null;
    
    /**
     * Constructor. 
     */
    public function __construct($entityManager, $authService) 
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
    }
    
    /**
     * Returns the current User or null if not logged in.
     * @param bool $useCachedUser If true, the User entity is fetched only on the first call (and cached on subsequent calls).
     * @return User|null
     */
    public function __invoke($useCachedUser = true)
    {
        // Check if User is already fetched previously.
        if ($useCachedUser && $this->user!==null)
            return $this->user;
        
        // Check if user is logged in.
        if ($this->authService->hasIdentity()) {
            
            // Fetch User entity from database.
            $this->user = $this->entityManager->getRepository(User::class)->findOneBy(array(
                'email' => $this->authService->getIdentity()
            ));
            if ($this->user==null) {
                // Oops.. the identity presents in session, but there is no such user in database.
                // We throw an exception, because this is a possible security problem. 
                throw new \Exception('Not found user with such ID');
            }
            
            // Return the User entity we found.
            return $this->user;
        }
        
        return null;
    }
}
