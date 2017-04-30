<?php

namespace meh\GsbBundle\Controller\Visiteur;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \DateTime;

class ConsulterFicheFraisController extends Controller
{
    public function pageConsulterFicheFraisAction()
    {
        $request = $this->container->get('request') ;
        
        if($request->getSession()->get('profil') != 'visiteur'){
            return $this->redirectToRoute('pageAuthentification');
        }
        
        $jourCourant = date('d') ; 
        $moisCourant = date('m');
        $anneeCourante = date('Y');
        $dateCourante = date('Y-m-d');
        
        $em = $this->getDoctrine()->getManager();
        
        $repository_ficheFrais = $em->getRepository('mehGsbBundle:Fichefrais');
        $fichesFrais = $repository_ficheFrais->findBy(array('idvisiteur' => $request->getSession()->get('idVisiteur')));
        
        $lesDatesPossibles = $this->getLesDatesPossibles($fichesFrais);
        
        $form = $this->createFormBuilder() 
                ->add('datesPossibles', 'choice', array('empty_value' => 'Periode d\'engagement', 'choices' => $lesDatesPossibles))
                ->add('voir', 'submit', array('label' => 'Consulter', 'attr' => array('class' => 'btn btn-primary')))
                ->getForm();
        
        $form->handleRequest($request) ;
        
        $laFicheFrais = array();
        $lesHorsForfait = array();
        $lesFrais = array();
        
        if($form->isValid()){
            
            $laFicheFrais = $repository_ficheFrais->find($form['datesPossibles']->getData());
            
            $repository_ligneHorsForfait = $em->getRepository('mehGsbBundle:Lignefraishorsforfait');
            $lesHorsForfait = $repository_ligneHorsForfait->findBy(array('idfiche' => $laFicheFrais));
            
            $repository_ligneFraisForfait = $em->getRepository('mehGsbBundle:Lignefraisforfait');
            $lesFraisForfait = $repository_ligneFraisForfait->findBy(array('idfichefrais' => $laFicheFrais));
            $lesFrais = $this->getTabFraisForfait($lesFraisForfait);
            
            return $this->render('mehGsbBundle:Visiteur:VueConsulterFicheFrais.html.twig', array('fraisForfait' => $lesFrais, 'horsForfait' => $lesHorsForfait,  'ficheFrais' => $laFicheFrais, 'form' => $form->createView())); 

        }
        
        return $this->render('mehGsbBundle:Visiteur:VueConsulterFicheFrais.html.twig', array('fraisForfait' => $lesFrais, 'horsForfait' => $lesHorsForfait,  'ficheFrais' => $laFicheFrais, 'form' => $form->createView())); 
    }
    
    public function getTabFraisForfait($lesFraisForfait) {
        
        $em = $this->getDoctrine()->getManager();
        $repository_frais = $em->getRepository('mehGsbBundle:Fraisforfait');
        $etape = $repository_frais->find(1);
        $km = $repository_frais->find(2);
        $nuitee = $repository_frais->find(3);
        $repas = $repository_frais->find(4);
        
        $lesFrais = array();
        
        foreach($lesFraisForfait as $unFrais){
                
            if($unFrais->getIdfrais() == $repas){
                $lesFrais['repasMidi'] = $unFrais->getQuantite();
            }
            if($unFrais->getIdfrais() == $etape){
                $lesFrais['etape'] = $unFrais->getQuantite();
            }
            if($unFrais->getIdfrais() == $km){
                $lesFrais['km'] = $unFrais->getQuantite();
            }
            if($unFrais->getIdfrais() == $nuitee){
                $lesFrais['nuitee'] = $unFrais->getQuantite();
            }
            $lesFrais['etat'] = $unFrais->getIdfichefrais()->getIdetatfraisforfait()->getLibelle();
        }
        
        return $lesFrais;
    }
    
    
    public function getLesDatesPossibles($lesFiches){
        
        $lesDates = array() ;
        foreach($lesFiches as $uneFiche){
            $date = $uneFiche->getDatecreation();
            $mois = $date->format('m');
            $annee = $date->format('Y');
            $moisFr = $this->getMoisLettre($mois);
            $lesDates[$uneFiche->getIdfiche()] = $moisFr." ".$annee ;
        }
        return $lesDates ;
    }
    
    
    public function getMoisLettre($moisChiffre) {
        switch($moisChiffre) {
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
                $moisLettre =''; 
                break;
        } 
        return $moisLettre ;
    }
 
}