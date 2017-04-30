<?php

namespace meh\GsbBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lignefraisforfait
 *
 * @ORM\Table(name="LigneFraisForfait", indexes={@ORM\Index(name="FK_LigneFraisForfait_FicheFrais", columns={"idFicheFrais"}), @ORM\Index(name="FK_LigneFraisForfait_FraisForfait", columns={"idFrais"})})
 * @ORM\Entity(repositoryClass="meh\GsbBundle\Entity\LigneFraisForfaitRepository")
 */
class Lignefraisforfait
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantite", type="integer", nullable=true)
     */
    private $quantite;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFrais", type="date", nullable=true)
     */
    private $datefrais;

    /**
     * @var \Fraisforfait
     *
     * @ORM\ManyToOne(targetEntity="Fraisforfait")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idFrais", referencedColumnName="idFrais")
     * })
     */
    private $idfrais;

    /**
     * @var \Fichefrais
     *
     * @ORM\ManyToOne(targetEntity="Fichefrais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idFicheFrais", referencedColumnName="idFiche")
     * })
     */
    private $idfichefrais;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set quantite
     *
     * @param integer $quantite
     * @return Lignefraisforfait
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Get quantite
     *
     * @return integer 
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * Set datefrais
     *
     * @param \DateTime $datefrais
     * @return Lignefraisforfait
     */
    public function setDatefrais($datefrais)
    {
        $this->datefrais = $datefrais;

        return $this;
    }

    /**
     * Get datefrais
     *
     * @return \DateTime 
     */
    public function getDatefrais()
    {
        return $this->datefrais;
    }

    /**
     * Set idfrais
     *
     * @param \meh\GsbBundle\Entity\Fraisforfait $idfrais
     * @return Lignefraisforfait
     */
    public function setIdfrais(\meh\GsbBundle\Entity\Fraisforfait $idfrais = null)
    {
        $this->idfrais = $idfrais;

        return $this;
    }

    /**
     * Get idfrais
     *
     * @return \meh\GsbBundle\Entity\Fraisforfait 
     */
    public function getIdfrais()
    {
        return $this->idfrais;
    }

    /**
     * Set idfichefrais
     *
     * @param \meh\GsbBundle\Entity\Fichefrais $idfichefrais
     * @return Lignefraisforfait
     */
    public function setIdfichefrais(\meh\GsbBundle\Entity\Fichefrais $idfichefrais = null)
    {
        $this->idfichefrais = $idfichefrais;

        return $this;
    }

    /**
     * Get idfichefrais
     *
     * @return \meh\GsbBundle\Entity\Fichefrais 
     */
    public function getIdfichefrais()
    {
        return $this->idfichefrais;
    }
}
