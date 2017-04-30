<?php

namespace meh\GsbBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fichefrais
 *
 * @ORM\Table(name="FicheFrais", indexes={@ORM\Index(name="FK_FicheFrais_EtatFiche", columns={"idEtatFiche"}), @ORM\Index(name="FK_FicheFrais_EtatFraisForfait", columns={"idEtatFraisForfait"}), @ORM\Index(name="FK_FicheFrais_EtatFraisHorsClassification", columns={"idEtatFraisHorsClassification"}), @ORM\Index(name="FK_FicheFrais_Visiteur", columns={"idVisiteur"})})
 * @ORM\Entity(repositoryClass="meh\GsbBundle\Entity\FicheFraisRepository")
 */
class Fichefrais
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idFiche", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idfiche;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="date", nullable=true)
     */
    private $datecreation;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbJustificatifs", type="integer", nullable=true)
     */
    private $nbjustificatifs;

    /**
     * @var string
     *
     * @ORM\Column(name="montantValide", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $montantvalide;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModif", type="date", nullable=true)
     */
    private $datemodif;

    /**
     * @var \Visiteur
     *
     * @ORM\ManyToOne(targetEntity="Visiteur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idVisiteur", referencedColumnName="idVisiteur")
     * })
     */
    private $idvisiteur;

    /**
     * @var \Etat
     *
     * @ORM\ManyToOne(targetEntity="Etat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idEtatFiche", referencedColumnName="idEtat")
     * })
     */
    private $idetatfiche;

    /**
     * @var \Etat
     *
     * @ORM\ManyToOne(targetEntity="Etat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idEtatFraisForfait", referencedColumnName="idEtat")
     * })
     */
    private $idetatfraisforfait;

    /**
     * @var \Etat
     *
     * @ORM\ManyToOne(targetEntity="Etat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idEtatFraisHorsClassification", referencedColumnName="idEtat")
     * })
     */
    private $idetatfraishorsclassification;



    /**
     * Get idfiche
     *
     * @return integer 
     */
    public function getIdfiche()
    {
        return $this->idfiche;
    }

    /**
     * Set datecreation
     *
     * @param \DateTime $datecreation
     * @return Fichefrais
     */
    public function setDatecreation($datecreation)
    {
        $this->datecreation = $datecreation;

        return $this;
    }

    /**
     * Get datecreation
     *
     * @return \DateTime 
     */
    public function getDatecreation()
    {
        return $this->datecreation;
    }

    /**
     * Set nbjustificatifs
     *
     * @param integer $nbjustificatifs
     * @return Fichefrais
     */
    public function setNbjustificatifs($nbjustificatifs)
    {
        $this->nbjustificatifs = $nbjustificatifs;

        return $this;
    }

    /**
     * Get nbjustificatifs
     *
     * @return integer 
     */
    public function getNbjustificatifs()
    {
        return $this->nbjustificatifs;
    }

    /**
     * Set montantvalide
     *
     * @param string $montantvalide
     * @return Fichefrais
     */
    public function setMontantvalide($montantvalide)
    {
        $this->montantvalide = $montantvalide;

        return $this;
    }

    /**
     * Get montantvalide
     *
     * @return string 
     */
    public function getMontantvalide()
    {
        return $this->montantvalide;
    }

    /**
     * Set datemodif
     *
     * @param \DateTime $datemodif
     * @return Fichefrais
     */
    public function setDatemodif($datemodif)
    {
        $this->datemodif = $datemodif;

        return $this;
    }

    /**
     * Get datemodif
     *
     * @return \DateTime 
     */
    public function getDatemodif()
    {
        return $this->datemodif;
    }

    /**
     * Set idvisiteur
     *
     * @param \meh\GsbBundle\Entity\Visiteur $idvisiteur
     * @return Fichefrais
     */
    public function setIdvisiteur(\meh\GsbBundle\Entity\Visiteur $idvisiteur = null)
    {
        $this->idvisiteur = $idvisiteur;

        return $this;
    }

    /**
     * Get idvisiteur
     *
     * @return \meh\GsbBundle\Entity\Visiteur 
     */
    public function getIdvisiteur()
    {
        return $this->idvisiteur;
    }

    /**
     * Set idetatfiche
     *
     * @param \meh\GsbBundle\Entity\Etat $idetatfiche
     * @return Fichefrais
     */
    public function setIdetatfiche(\meh\GsbBundle\Entity\Etat $idetatfiche = null)
    {
        $this->idetatfiche = $idetatfiche;

        return $this;
    }

    /**
     * Get idetatfiche
     *
     * @return \meh\GsbBundle\Entity\Etat 
     */
    public function getIdetatfiche()
    {
        return $this->idetatfiche;
    }

    /**
     * Set idetatfraisforfait
     *
     * @param \meh\GsbBundle\Entity\Etat $idetatfraisforfait
     * @return Fichefrais
     */
    public function setIdetatfraisforfait(\meh\GsbBundle\Entity\Etat $idetatfraisforfait = null)
    {
        $this->idetatfraisforfait = $idetatfraisforfait;

        return $this;
    }

    /**
     * Get idetatfraisforfait
     *
     * @return \meh\GsbBundle\Entity\Etat 
     */
    public function getIdetatfraisforfait()
    {
        return $this->idetatfraisforfait;
    }

    /**
     * Set idetatfraishorsclassification
     *
     * @param \meh\GsbBundle\Entity\Etat $idetatfraishorsclassification
     * @return Fichefrais
     */
    public function setIdetatfraishorsclassification(\meh\GsbBundle\Entity\Etat $idetatfraishorsclassification = null)
    {
        $this->idetatfraishorsclassification = $idetatfraishorsclassification;

        return $this;
    }

    /**
     * Get idetatfraishorsclassification
     *
     * @return \meh\GsbBundle\Entity\Etat 
     */
    public function getIdetatfraishorsclassification()
    {
        return $this->idetatfraishorsclassification;
    }
}
