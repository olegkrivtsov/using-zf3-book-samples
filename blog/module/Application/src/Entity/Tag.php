<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * This class represents a tag.
 * @ORM\Entity
 * @ORM\Table(name="tag")
 */
class Tag 
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /** 
     * @ORM\Column(name="name") 
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="\Application\Entity\Post", mappedBy="tags")
     */
    protected $posts;
    
    /**
     * Constructor.
     */
    public function __construct() 
    {        
        $this->posts = new ArrayCollection();        
    }

    /**
     * Returns ID of this tag.
     * @return integer
     */
    public function getId() 
    {
        return $this->id;
    }

    /**
     * Sets ID of this tag.
     * @param int $id
     */
    public function setId($id) 
    {
        $this->id = $id;
    }

    /**
     * Returns name.
     * @return string
     */
    public function getName() 
    {
        return $this->name;
    }

    /**
     * Sets name.
     * @param string $name
     */
    public function setName($name) 
    {
        $this->name = $name;
    }
    
    /**
     * Returns posts which have this tag.
     * @return type
     */
    public function getPosts() 
    {
        return $this->posts;
    }
    
    /**
     * Adds a post which has this tag.
     * @param type $post
     */
    public function addPost($post) 
    {
        $this->posts[] = $post;        
    }
}

