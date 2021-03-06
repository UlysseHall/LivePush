<?php

namespace SocialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fichier
 *
 * @ORM\Table(name="fichier")
 * @ORM\Entity(repositoryClass="SocialBundle\Repository\FichierRepository")
 */
class Fichier
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="pathName", type="string", length=255)
     */
    private $pathName;
    
    /**
     * @ORM\ManyToOne(targetEntity="SocialBundle\Entity\Problem")
     * @ORM\JoinColumn(nullable=false)
     */
    private $problem;
    
    /**
     * @ORM\OneToOne(targetEntity="SocialBundle\Entity\Comment")
     */
    private $comment;
    
    /**
     * @var bool
     *
     *@ORM\Column(name="image", type="boolean")
     */
    private $image;
    
    public function __construct()
	{
		$this->image = false;
	}


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Fichier
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set problem
     *
     * @param \SocialBundle\Entity\Problem $problem
     *
     * @return Fichier
     */
    public function setProblem(\SocialBundle\Entity\Problem $problem)
    {
        $this->problem = $problem;

        return $this;
    }

    /**
     * Get problem
     *
     * @return \SocialBundle\Entity\Problem
     */
    public function getProblem()
    {
        return $this->problem;
    }

    /**
     * Set pathName
     *
     * @param string $pathName
     *
     * @return Fichier
     */
    public function setPathName($pathName)
    {
        $this->pathName = $pathName;

        return $this;
    }

    /**
     * Get pathName
     *
     * @return string
     */
    public function getPathName()
    {
        return $this->pathName;
    }

    /**
     * Set comment
     *
     * @param \SocialBundle\Entity\Comment $comment
     *
     * @return Fichier
     */
    public function setComment(\SocialBundle\Entity\Comment $comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return \SocialBundle\Entity\Comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set image
     *
     * @param boolean $image
     *
     * @return Fichier
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return boolean
     */
    public function getImage()
    {
        return $this->image;
    }
}
