<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a comment related to a blog post.
 * @ORM\Entity
 * @ORM\Table(name="comment")
 */
class Comment 
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /** 
     * @ORM\Column(name="content")  
     */
    protected $content;

    /** 
     * @ORM\Column(name="author")  
     */
    protected $author;
    
    /** 
     * @ORM\Column(name="date_created")  
     */
    protected $dateCreated;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    protected $post;
    
    /**
     * Returns ID of this comment.
     * @return integer
     */
    public function getId() 
    {
        return $this->id;
    }

    /**
     * Sets ID of this comment.
     * @param int $id
     */
    public function setId($id) 
    {
        $this->id = $id;
    }
    
    /**
     * Returns comment text.
     * @return string
     */
    public function getContent() 
    {
        return $this->content;
    }

    /**
     * Sets comment text.
     * @param string $comment
     */
    public function setContent($comment) 
    {
        $this->content = $comment;
    }
    
    /**
     * Returns author's name.
     * @return string
     */
    public function getAuthor() 
    {
        return $this->author;
    }

    /**
     * Sets author's name.
     * @param string $author
     */
    public function setAuthor($author) 
    {
        $this->author = $author;
    }

    /**
     * Returns the date when this post was created.
     * @return string
     */
    public function getDateCreated() 
    {
        return $this->dateCreated;
    }
    
    /**
     * Sets the date when this post was created.
     * @param string $dateCreated
     */
    public function setDateCreated($dateCreated) 
    {
        $this->dateCreated = (string)$dateCreated;
    }
    
    /*
     * Returns associated post.
     * @return \Application\Entity\Post
     */
    public function getPost() 
    {
        return $this->post;
    }
    
    /**
     * Sets associated post.
     * @param \Application\Entity\Post $post
     */
    public function setPost($post) 
    {
        $this->post = $post;
        $post->addComment($this);
    }
}

