<?php

namespace SocialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Problem
 *
 * @ORM\Table(name="problem")
 * @ORM\Entity(repositoryClass="SocialBundle\Repository\ProblemRepository")
 */
class Problem
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     *
     * @ORM\JoinColumn(nullable=false)
     */
    private $auteur;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text")
     */
    private $contenu;

    /**
     * @var string
     *
     * @ORM\Column(name="langage", type="string", length=255)
     */
    private $langage;

    /**
     * @var bool
     *
     * @ORM\Column(name="resolu", type="boolean")
     */
    private $resolu;

	
	public function __construct()
	{
		$this->date = new \Datetime();
		$this->resolu = false;
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Problem
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return Problem
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set auteur
     *
     * @param string $auteur
     *
     * @return Problem
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return string
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Problem
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set langage
     *
     * @param string $langage
     *
     * @return Problem
     */
    public function setLangage($langage)
    {
        $this->langage = $langage;

        return $this;
    }

    /**
     * Get langage
     *
     * @return string
     */
    public function getLangage()
    {
        return $this->langage;
    }

    /**
     * Set resolu
     *
     * @param boolean $resolu
     *
     * @return Problem
     */
    public function setResolu($resolu)
    {
        $this->resolu = $resolu;

        return $this;
    }

    /**
     * Get resolu
     *
     * @return bool
     */
    public function getResolu()
    {
        return $this->resolu;
    }
}
