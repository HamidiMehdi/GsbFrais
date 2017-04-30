<?php

namespace meh\GsbBundle\Modele;

class ModeleGsb {
    
    public function __construct() {
        
    }
       
    /* Connexion a la base de données
     * Pas d'argument a faire passer
     * retourn true si elle ce connecte sinon elle retourne false 
     */
    public function connexionBDD(){ 
	try {
            $pdo = new \PDO('mysql:host=localhost;dbname=gsb;charset=utf8', 'root', 'azerty');
	}
	catch(PDOException $e){
            die('Erreur : '.$e->getMessage());
        }
        return $pdo ;
           
    }
    
    /* Cherche un frais forfait
     * Arguments a faire passer : moisCourant, anneeCourant, idVisiteur
     * Retourne true si un tuple a été trouver, sinon false
     */ 
    public function getFicheFraisCourante($moisCourant, $anneeCourant, $idVisiteur){
        $pdo = $this->connexionBDD();
        $sql =      "Select idFiche "
                .   "From FicheFrais "
                .   "where dateCreation like '$anneeCourant-$moisCourant-__' "
                .   "and idVisiteur = $idVisiteur " ;
        try{			
            $query = $pdo->query($sql);
            $resultat = $query->fetchAll();
	}
	catch(PDOException $e){
            echo $e->getMessage();
            die ;
	}
	
	return $resultat ;
    }
    
    
    /* Cherche une fiche de frais d'un visiteur a un mois et une annee donnée
     * Arguments a faire passer : idVisiteur, mois et annee
     * Retourne true si la mise a jour a été faite, sinon false
     */ 
    function getFicheVisiteurPeriode($idVisiteur, $annee, $mois){  
        
	$pdo = $this->connexionBDD();
        $sql = "select * from FicheFrais where idVisiteur = $idVisiteur and dateCreation like '$annee-$mois-__';" ;
        try{			
            $query = $pdo->query($sql);
            $resultat = $query->fetchAll();
	}
	catch(PDOException $e){
            echo $e->getMessage();
            die ;
	}
	return $resultat ;
    }
    
}
