<?php

namespace meh\GsbBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fraisforfait
 *
 * @ORM\Table(name="FraisForfait")
 * @ORM\Entity(repositoryClass="meh\GsbBundle\Entity\FraisForfaitRepository")
 */
class Fraisforfait
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idFrais", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idfrais;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=3, nullable=true)
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=30, nullable=true)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="montant", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $montant;



    /**
     * Get idfrais
     *
     * @return integer 
     */
    public function getIdfrais()
    {
        return $this->idfrais;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return Fraisforfait
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string 
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     * @return Fraisforfait
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set montant
     *
     * @param string $montant
     * @return Fraisforfait
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return string 
     */
    public function getMontant()
    {
        return $this->montant;
    }
}
