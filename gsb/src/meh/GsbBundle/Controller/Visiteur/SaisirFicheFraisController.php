<?php

namespace meh\GsbBundle\Controller\Visiteur;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use meh\GsbBundle\Entity\Fichefrais;
use meh\GsbBundle\Entity\Lignefraisforfait;
use meh\GsbBundle\Entity\Lignefraishorsforfait;
use meh\GsbBundle\Modele\ModeleGsb;

class SaisirFicheFraisController extends Controller
{
    public function pageSaisirFicheFraisAction()
    {
        $request = $this->container->get('request') ;
        
        if($request->getSession()->get('profil') != 'visiteur'){
            return $this->redirectToRoute('pageAuthentification');
        }
        
        $em = $this->getDoctrine()->getManager() ;
        $repository_Fichefrais = $em->getRepository('mehGsbBundle:Fichefrais');
        $repository_Lignefraisforfait = $em->getRepository('mehGsbBundle:Lignefraisforfait');
        
        $jourCourant = date('d') ; 
        $moisCourant = date('m');
        $anneeCourant = date('Y');
        
        $moisLettre = $this->getMoisEnLettre($moisCourant);
        $moisEtAnnee = $moisLettre." ".$anneeCourant ;
        
        $modele = new ModeleGsb();
        $existFiche = $modele->getFicheFraisCourante($moisCourant, $anneeCourant, $request->getSession()->get('idVisiteur')); // recherche une ficghe de frais qui le mois et l'année courante du visiteur 
        
        if($existFiche != null){ // si la fiche exist
            
            $ficheFrais = $repository_Fichefrais->find($existFiche[0]['idFiche']);
            $lesLignesFraisTab = $repository_Lignefraisforfait->findBy(array('idfichefrais' => $ficheFrais));
            $lesLignesFrais = $this->getLignesFrais($lesLignesFraisTab);
            
            $formFraisForfait = $this->createFormBuilder()
                    ->add('repasMidi', 'text', array('data' => $lesLignesFrais[4], 'attr' => array('pattern'=>'[0-9]+', 'title'=>'Nombre entier supérieur à 0')))
                    ->add('nuitee', 'text', array('data' => $lesLignesFrais[3], 'label' => 'Nuitée', 'attr' => array('pattern'=>'[0-9]+', 'title'=>'Nombre entier supérieur à 0')))
                    ->add('etape', 'text', array('data' => $lesLignesFrais[1], 'attr' => array('pattern'=>'[0-9]+', 'title'=>'Nombre entier supérieur à 0')))
                    ->add('km', 'text', array('data' => $lesLignesFrais[2], 'attr' => array('pattern'=>'[0-9]+', 'title'=>'Nombre entier supérieur à 0')))
                    ->add('valider', 'submit', array('label' => 'Mettre à jour', 'attr' => array('class' => 'btn btn-primary')))
                    ->add('annuler', 'reset', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-danger')))
                    ->getForm() ;
            $formFraisForfait->handleRequest($request);

                // ------------------------------------------------------------------------ //

            $listeJours = $this->getListeJours();
            $formHorsForfait = $this->createFormBuilder() 
                    ->add('jour', 'choice', array('empty_value' => '-- Jour --', 'choices' => $listeJours))
                    ->add('mois', 'text', array('attr' => array('disabled' => true), 'data' => $moisCourant))
                    ->add('libelle','text')
                    ->add('montant', 'number', array('attr' => array('pattern'=>'[0-9]+(\.[0-9][0-9]?)?', 'title'=>'20.00 par exemple')))
                    ->add('ajouter','submit', array('attr' => array('class'=>'btn btn-primary')))
                    ->getForm();
            $formHorsForfait->handleRequest($request) ;
    
             // ------------------------------------------------------------------------ //
        
            $formHorsClassification = $this->createFormBuilder()
                    ->add('justificatif', 'text', array('data' => $ficheFrais->getNbjustificatifs(), 'label' =>'Nombre justificatif', 'attr' => array('pattern'=>'[0-9]+', 'title'=>'Nombre entier supérieur à 0')))
                    ->add('montant', 'text', array('data' => $ficheFrais->getMontantvalide(), 'label' => 'Montant total', 'attr' => array('pattern'=>'[-+]?[0-9]+(\.[0-9]+)?', 'title'=>'Nombres réels supérieur à 0'))) 
                    ->add('ajouter', 'submit', array('label' => 'Mettre à jour', 'attr' => array('class' => 'btn btn-primary')))
                    ->add('annuler', 'reset', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-danger')))
                    ->getForm() ;
            $formHorsClassification->handleRequest($request);
         
            if($formFraisForfait->isValid()){
                
                $repository_frais = $em->getRepository('mehGsbBundle:Fraisforfait');
                $etape = $repository_frais->find(1);
                $km = $repository_frais->find(2);
                $nuitee = $repository_frais->find(3);
                $repas = $repository_frais->find(4);
                
                foreach($lesLignesFraisTab as $uneLigneFrais){
                    
                    if($uneLigneFrais->getIdfrais() == $etape){
                        $uneLigneFrais->setQuantite($formFraisForfait['etape']->getData());
                    }
                    if($uneLigneFrais->getIdfrais() == $km){
                        $uneLigneFrais->setQuantite($formFraisForfait['km']->getData());
                    } 
                    if($uneLigneFrais->getIdfrais() == $nuitee){
                        $uneLigneFrais->setQuantite($formFraisForfait['nuitee']->getData());
                    } 
                    if($uneLigneFrais->getIdfrais() == $repas){
                        $uneLigneFrais->setQuantite($formFraisForfait['repasMidi']->getData());
                    }
                    $em->persist($uneLigneFrais);
                }
                
                $ficheFrais->setDatemodif(date_create(date('Y-m-d')));
                $em->persist($ficheFrais);
                $em->flush();

                return $this->redirectToRoute('pageSaisirFicheFrais') ;
            }
            else if($formHorsForfait->isValid()){
                
                $repository_etat = $em->getRepository('mehGsbBundle:Etat'); 
                $etat = $repository_etat->find(6); // etat (Enregistré)
                
                $jour = $formHorsForfait['jour']->getData() ;
                $libelle = $formHorsForfait['libelle']->getData() ;
                $montant = $formHorsForfait['montant']->getData() ;
                
                $date = date_create(date($anneeCourant.'-'.$moisCourant.'-'.$jour));
                
                $ligneHorsForfait = new Lignefraishorsforfait();
                $ligneHorsForfait->setLibelle($libelle);
                $ligneHorsForfait->setDatefrais($date);
                $ligneHorsForfait->setMontant($montant);
                $ligneHorsForfait->setRefuser(false);
                $ligneHorsForfait->setIdfiche($ficheFrais);
                $ligneHorsForfait->setIdetat($etat);
                
                $em->persist($ligneHorsForfait);
                $em->flush();
                
                return $this->redirectToRoute('pageSaisirFicheFrais');
                
            }
            else if($formHorsClassification->isValid()){
                
                $ficheFrais->setNbjustificatifs($formHorsClassification['justificatif']->getData());
                $ficheFrais->setMontantvalide($formHorsClassification['montant']->getData());
                $ficheFrais->setDatemodif(date_create(date('Y-m-d')));
                $em->persist($ficheFrais);
                $em->flush();
                
                return $this->redirectToRoute('pageSaisirFicheFrais');
            }

        }
        else{
            $this->clotureFicheAvant();
            $this->creerFiche();
            return $this->redirectToRoute('pageSaisirFicheFrais');
        }
        
        $repository_LigneFraisHorsforfait = $em->getRepository('mehGsbBundle:Lignefraishorsforfait');
        $lesHorsForfait = $repository_LigneFraisHorsforfait->findBy(array('idfiche' => $ficheFrais)); // recup des hors forfaits
        
        return $this->render('mehGsbBundle:Visiteur:VueSaisirFicheFrais.html.twig', array('lesHorsForfait' => $lesHorsForfait, 'derniereModif' => $ficheFrais->getDatemodif(), 'moisEtAnnee' => $moisEtAnnee, 'formFraisForfait' => $formFraisForfait->createView(), 'formHorsForfait' => $formHorsForfait->createView(), 'formHorsClassification' => $formHorsClassification->createView())); 

    }
    
    public function getLignesFrais($lesLignes) {
        
        $em = $this->getDoctrine()->getManager();
        
        $repository_frais = $em->getRepository('mehGsbBundle:Fraisforfait');
        $etape = $repository_frais->find(1);
        $km = $repository_frais->find(2);
        $nuitee = $repository_frais->find(3);
        $repas = $repository_frais->find(4);
        
        $données = array();
        foreach($lesLignes as $uneLigne){
            if($uneLigne->getIdfrais() == $etape){
                $données[1] = $uneLigne->getQuantite();
            }
            if($uneLigne->getIdfrais() == $km){
                $données[2] = $uneLigne->getQuantite();
            }
            if($uneLigne->getIdfrais() == $nuitee){
                $données[3] = $uneLigne->getQuantite();
            }
            if($uneLigne->getIdfrais() == $repas){
                $données[4] = $uneLigne->getQuantite();
            }
        }
        return $données;
    }
    
    public function clotureFicheAvant() {
        
        $request = $this->container->get('request') ;
        
        $em = $this->getDoctrine()->getManager();
        
        $repository_etat = $em->getRepository('mehGsbBundle:Etat'); 
        $etat = $repository_etat->find(2); // etat (Saisie clôturée)
        
        $repository_visiteur = $em->getRepository('mehGsbBundle:Visiteur');
        $visiteur = $repository_visiteur->find($request->getSession()->get('idVisiteur')); // le visiteur
        
        $repository_Fichefrais = $em->getRepository('mehGsbBundle:Fichefrais');
        $fichesFraisVisiteur = $repository_Fichefrais->findBy(array('idvisiteur' => $visiteur)); // toutes les fiches de frais du visiteur
        
        if($fichesFraisVisiteur != null){
            
            foreach($fichesFraisVisiteur as $uneFiche){
                $uneFiche->setIdetatfiche($etat); // on modifie l'etat de toutes les fiches d'avant ce mois
                $em->persist($uneFiche);
            }
            $em->flush();
        }
    }
    
    public function creerFiche() {
        
        $request = $this->container->get('request') ;
        
        $em = $this->getDoctrine()->getManager();
        
        $repository_etat = $em->getRepository('mehGsbBundle:Etat'); 
        $etat1 = $repository_etat->find(1); // etat (Fiche créée, saisie en cours)
        $etat6 = $repository_etat->find(6); // etat (Enregistré)
        
        $repository_visiteur = $em->getRepository('mehGsbBundle:Visiteur');
        $visiteur = $repository_visiteur->find($request->getSession()->get('idVisiteur')); // le visiteur
        
        $newFicheFrais = new Fichefrais();
        $newFicheFrais->setDatecreation(date_create(date('Y-m-d')));
        $newFicheFrais->setNbjustificatifs(0);
        $newFicheFrais->setMontantvalide(0);
        $newFicheFrais->setDatemodif(date_create(date('Y-m-d')));
        $newFicheFrais->setIdetatfiche($etat1);
        $newFicheFrais->setIdvisiteur($visiteur);
        $newFicheFrais->setIdetatfraisforfait($etat6);
        $newFicheFrais->setIdetatfraishorsclassification($etat6);
        
        $em->persist($newFicheFrais);
        
        $repository_frais = $em->getRepository('mehGsbBundle:Fraisforfait');
        $etape = $repository_frais->find(1);
        $km = $repository_frais->find(2);
        $nuitee = $repository_frais->find(3);
        $repas = $repository_frais->find(4);
        
        $fraisEtape = new Lignefraisforfait();
        $fraisEtape->setQuantite(0);
        $fraisEtape->setDatefrais(date_create(date('Y-m-d')));
        $fraisEtape->setIdfichefrais($newFicheFrais);
        $fraisEtape->setIdfrais($etape);
        
        $fraisKm = new Lignefraisforfait();
        $fraisKm->setQuantite(0);
        $fraisKm->setDatefrais(date_create(date('Y-m-d')));
        $fraisKm->setIdfichefrais($newFicheFrais);
        $fraisKm->setIdfrais($km);        
        
        $fraisNuitee = new Lignefraisforfait();
        $fraisNuitee->setQuantite(0);
        $fraisNuitee->setDatefrais(date_create(date('Y-m-d')));
        $fraisNuitee->setIdfichefrais($newFicheFrais);
        $fraisNuitee->setIdfrais($nuitee);
        
        $fraisRepas = new Lignefraisforfait();
        $fraisRepas->setQuantite(0);
        $fraisRepas->setDatefrais(date_create(date('Y-m-d')));
        $fraisRepas->setIdfichefrais($newFicheFrais);
        $fraisRepas->setIdfrais($repas);
        
        $em->persist($fraisEtape);
        $em->persist($fraisKm);
        $em->persist($fraisNuitee);
        $em->persist($fraisRepas);
        
        $em->flush();
    }
    
    public function getListeJours(){
        $listeJours = array() ;
        $jourCourant = date('d');
        for($i = 1; $i <= $jourCourant; $i++){
            $listeJours[$i] = $i ;
        }
        return $listeJours;
        
    }
     
     public function getMoisEnLettre($mois) {
        switch($mois) {
            case '01': 
                $moisLettre = 'Janvier'; 
                break;
            case '02': 
                $moisLettre = 'Février'; 
                break;
            case '03': 
                $moisLettre = 'Mars'; 
                break;
            case '04': 
                $moisLettre = 'Avril'; 
                break;
            case '05': 
                $moisLettre = 'Mai'; 
                break;
            case '06': 
                $moisLettre = 'Juin'; 
                break;
            case '07': 
                $moisLettre = 'Juillet'; 
                break;
            case '08': 
                $moisLettre = 'Août'; 
                break;
            case '09': 
                $moisLettre = 'Septembre'; 
                break;
            case '10': 
                $moisLettre = 'Octobre'; 
                break;
            case '11': 
                $moisLettre = 'Novembre'; 
                break;
            case '12': 
                $moisLettre = 'Decembre'; 
                break;
            default: 
                break;
        } 
        return $moisLettre ;
    }
    
    public function deleteHorsForfaitAction($id) {
        
        $request = $this->container->get('request') ;
        
        if($request->getSession()->get('profil') != 'visiteur'){
            return $this->redirectToRoute('pageAuthentification');
        }
        
        $em = $this->getDoctrine()->getManager();
        $repository_LigneFraisHorsforfaits = $em->getRepository('mehGsbBundle:Lignefraishorsforfait'); 
        $horsForfait = $repository_LigneFraisHorsforfaits->find($id);
        
        $em->remove($horsForfait);
        $em->flush();
        
        return $this->redirectToRoute('pageSaisirFicheFrais');
        
    }
}