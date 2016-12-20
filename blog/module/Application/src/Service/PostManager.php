<?php
namespace Application\Service;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Application\Entity\Post;
use Application\Entity\Comment;
use Application\Entity\Tag;
use Zend\Filter\StaticFilter;

/**
 * The PostManager service is responsible for adding new posts, updating existing
 * posts, adding tags to post, etc.
 */
class PostManager
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager;
     */
    private $entityManager;
    
    /**
     * Constructor.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * This method adds a new post.
     */
    public function addNewPost($data) 
    {
        // Create new Post entity.
        $post = new Post();
        $post->setTitle($data['title']);
        $post->setContent($data['content']);
        $post->setStatus($data['status']);
        $currentDate = date('Y-m-d H:i:s');
        $post->setDateCreated($currentDate);        
        
        // Add the entity to entity manager.
        $this->entityManager->persist($post);
        
        // Add tags to post
        $this->addTagsToPost($data['tags'], $post);
        
        // Apply changes to database.
        $this->entityManager->flush();
    }
    
    /**
     * This method allows to update data of a single post.
     */
    public function updatePost($post, $data) 
    {
        $post->setTitle($data['title']);
        $post->setContent($data['content']);
        $post->setStatus($data['status']);
        
        // Add tags to post
        $this->addTagsToPost($data['tags'], $post);
        
        // Apply changes to database.
        $this->entityManager->flush();
    }

    /**
     * Adds/updates tags in the given post.
     */
    private function addTagsToPost($tagsStr, $post) 
    {
        // Remove tag associations (if any)
        $tags = $post->getTags();
        foreach ($tags as $tag) {            
            $post->removeTagAssociation($tag);
        }
        
        // Add tags to post
        $tags = explode(',', $tagsStr);
        foreach ($tags as $tagName) {
            
            $tagName = StaticFilter::execute($tagName, 'StringTrim');
            if (empty($tagName)) {
                continue; 
            }
            
            $tag = $this->entityManager->getRepository(Tag::class)
                    ->findOneByName($tagName);
            if ($tag == null)
                $tag = new Tag();
            
            $tag->setName($tagName);
            $tag->addPost($post);
            
            $this->entityManager->persist($tag);
            
            $post->addTag($tag);
        }
    }    
    
    /**
     * Returns status as a string.
     */
    public function getPostStatusAsString($post) 
    {
        switch ($post->getStatus()) {
            case Post::STATUS_DRAFT: return 'Draft';
            case Post::STATUS_PUBLISHED: return 'Published';
        }
        
        return 'Unknown';
    }
    
    /**
     * Converts tags of the given post to comma separated list (string).
     */
    public function convertTagsToString($post) 
    {
        $tags = $post->getTags();
        $tagCount = count($tags);
        $tagsStr = '';
        $i = 0;
        foreach ($tags as $tag) {
            $i ++;
            $tagsStr .= $tag->getName();
            if ($i < $tagCount) 
                $tagsStr .= ', ';
        }
        
        return $tagsStr;
    }    

    /**
     * Returns count of comments for given post as properly formatted string.
     */
    public function getCommentCountStr($post)
    {
        $commentCount = count($post->getComments());
        if ($commentCount == 0)
            return 'No comments';
        else if ($commentCount == 1) 
            return '1 comment';
        else
            return $commentCount . ' comments';
    }


    /**
     * This method adds a new comment to post.
     */
    public function addCommentToPost($post, $data) 
    {
        // Create new Comment entity.
        $comment = new Comment();
        $comment->setPost($post);
        $comment->setAuthor($data['author']);
        $comment->setContent($data['comment']);        
        $currentDate = date('Y-m-d H:i:s');
        $comment->setDateCreated($currentDate);

        // Add the entity to entity manager.
        $this->entityManager->persist($comment);

        // Apply changes.
        $this->entityManager->flush();
    }
    
    /**
     * Removes post and all associated comments.
     */
    public function removePost($post) 
    {
        // Remove associated comments
        $comments = $post->getComments();
        foreach ($comments as $comment) {
            $this->entityManager->remove($comment);
        }
        
        // Remove tag associations (if any)
        $tags = $post->getTags();
        foreach ($tags as $tag) {
            
            $post->removeTagAssociation($tag);
        }
        
        $this->entityManager->remove($post);
        
        $this->entityManager->flush();
    }
    
    /**
     * Calculates frequencies of tag usage.
     */
    public function getTagCloud()
    {
        $tagCloud = [];
                
        $posts = $this->entityManager->getRepository(Post::class)
                    ->findPostsHavingAnyTag();
        $totalPostCount = count($posts);
        
        $tags = $this->entityManager->getRepository(Tag::class)
                ->findAll();
        foreach ($tags as $tag) {
            
            $postsByTag = $this->entityManager->getRepository(Post::class)
                    ->findPostsByTag($tag->getName())->getResult();
            
            $postCount = count($postsByTag);
            if ($postCount > 0) {
                $tagCloud[$tag->getName()] = $postCount;
            }
        }
        
        $normalizedTagCloud = [];
        
        // Normalize
        foreach ($tagCloud as $name=>$postCount) {
            $normalizedTagCloud[$name] =  $postCount/$totalPostCount;
        }
        
        return $normalizedTagCloud;
    }
}



