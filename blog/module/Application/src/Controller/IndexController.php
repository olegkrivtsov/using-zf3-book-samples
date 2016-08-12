<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Service\MailSender;
use Application\Entity\Post;

/**
 * This is the main controller class of the Blog application. The 
 * controller class is used to receive user input,  
 * pass the data to the models and pass the results returned by models to the 
 * view for rendering.
 */
class IndexController extends AbstractActionController 
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager 
     */
    public $entityManager;
    
    /**
     * Post manager.
     * @var Application\Service\PostManager 
     */
    private $postManager;
    
    /**
     * Constructor is used for injecting dependencies into the controller.
     */
    public function __construct($entityManager, $postManager) 
    {
        $this->entityManager = $entityManager;
        $this->postManager = $postManager;
    }
    
    /**
     * This is the default "index" action of the controller. It displays the 
     * Recent Posts page containing the recent blog posts.
     */
    public function indexAction() 
    {
        $tagFilter = $this->params()->fromQuery('tag', null);
        
        if ($tagFilter) {
         
            // Filter posts by tag
            $posts = $this->entityManager->getRepository(Post::class)
                    ->findPostsByTag($tagFilter);
            
        } else {
            // Get recent posts
            $posts = $this->entityManager->getRepository(Post::class)
                    ->findBy(['status'=>Post::STATUS_PUBLISHED], 
                             ['dateCreated'=>'DESC']);
        }
        
        // Get popular tags.
        $tagCloud = $this->postManager->getTagCloud();
        
        // Render the view template.
        return new ViewModel([
            'posts' => $posts,
            'postManager' => $this->postManager,
            'tagCloud' => $tagCloud
        ]);
    }
    
    /**
     * This action displays the About page.
     */
    public function aboutAction() 
    {   
        $appName = 'Blog';
        $appDescription = 'A simple blog application for the Using Zend Framework 3 book';
        
        return new ViewModel([
            'appName' => $appName,
            'appDescription' => $appDescription
        ]);
    }
}
