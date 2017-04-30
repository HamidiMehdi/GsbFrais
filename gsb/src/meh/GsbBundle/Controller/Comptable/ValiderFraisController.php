<?php

namespace meh\GsbBundle\Controller\Comptable;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use meh\GsbBundle\Modele\ModeleGsb;

class ValiderFraisController extends Controller
{
    public function pageValiderFraisAction()
    {
        $request = $this->container->get('request') ;
        
        $em = $this->getDoctrine()->getManager();
        
        $anneeCourante = date('Y');
        $aucuneFiche = "";
        
        $visiteurs = $this->getLesVisiteurs();
        $lesMois = array('01' => 'Janvier', '02' => 'Février', '03' => 'Mars', '04' => 'Avril', '05' => 'Mai', '06' => 'Juin', '07' => 'Juillet', '08' => 'Aout', '09' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre');
        
        $form = $this->createFormBuilder() 
                ->add('visiteurs', 'choice', array('empty_value' => 'Visiteur', 'choices' => $visiteurs))
                ->add('mois', 'choice', array('empty_value' => 'Mois', 'choices' => $lesMois))
                ->add('voir', 'submit', array('label' => 'Valider', 'attr' => array('class' => 'btn btn-primary')))
                ->getForm();
        
        $form->handleRequest($request) ;
        
        if($form->isValid()){
            
            $repository_visiteur = $em->getRepository('mehGsbBundle:Visiteur');
            $visiteur = $repository_visiteur->find($form['visiteurs']->getData());
            $modele = new ModeleGsb();
            $existeFiche = $modele->getFicheVisiteurPeriode($visiteur->getIdvisiteur(), $anneeCourante, $form['mois']->getData());
            
            if($existeFiche != null){
                return $this->redirectToRoute('pageValiderFraisDunVisiteur', array('idFiche' => $existeFiche[0]['idFiche']));
            }
            else{
                $aucuneFiche = "Pas de fiche de frais pour ce visiteur ce mois.";
                return $this->render('mehGsbBundle:Comptable:VueValiderFrais.html.twig', array('aucuneFiche' => $aucuneFiche, 'form' => $form->createView())); 
            }
        }
        
        return $this->render('mehGsbBundle:Comptable:VueValiderFrais.html.twig', array('aucuneFiche' => $aucuneFiche, 'form' => $form->createView())); 
    }
    
    
    public function getLesVisiteurs(){ // retourne un array avec comme syntaxe $tab[idVisiteur] = nom.prenom
        
        $request = $this->container->get('request') ;
        $em = $this->getDoctrine()->getManager();
        
        $repository_comptable = $em->getRepository('mehGsbBundle:Comptable');
        $comptable = $repository_comptable->find($request->getSession()->get('idComptable'));
        $repository_visiteur = $em->getRepository('mehGsbBundle:Visiteur');
        $visiteurs = $repository_visiteur->findBy(array('idcomptable' => $comptable));
        
        $lesVisiteurs = array();
        if($visiteurs != null){
            foreach($visiteurs as $unVisiteur){
                $lesVisiteurs[$unVisiteur->getIdvisiteur()] = $unVisiteur->getNom()." ".$unVisiteur->getPrenom() ;
            }
        }
        return $lesVisiteurs;
    }
    
    
    public function pageValiderFraisDunVisiteurAction($idFiche)
    {
        
        $request = $this->container->get('request') ;
        
        $em = $this->getDoctrine()->getManager();
                
        $repository_ficheFais = $em->getRepository('mehGsbBundle:Fichefrais');
        $ficheFrais = $repository_ficheFais->find($idFiche);
        
        $repository_visiteur = $em->getRepository('mehGsbBundle:Visiteur');
        $visiteur = $repository_visiteur->find($ficheFrais->getIdvisiteur());
                
        $repository_Lignefraisforfait = $em->getRepository('mehGsbBundle:Lignefraisforfait');
        $lesLignesFraisTab = $repository_Lignefraisforfait->findBy(array('idfichefrais' => $ficheFrais));
        $lesLignesFrais = $this->getLignesFrais($lesLignesFraisTab);
        
        $repository_LigneFraisHorsforfait = $em->getRepository('mehGsbBundle:Lignefraishorsforfait');
        $lesHorsForfait = $repository_LigneFraisHorsforfait->findBy(array('idfiche' => $ficheFrais)); // recup des hors forfaits
        
        $lesEtat = array('3' => 'Validée', '4' => 'Mise en paiement', '5' => 'Remboursée', '6' => 'Enregistré');
        
        $formFraisForfait = $this->createFormBuilder()
                ->add('repasMidi', 'text', array('data' => $lesLignesFrais[4], 'attr' => array('pattern'=>'[0-9]+', 'title'=>'Nombre entier supérieur à 0')))
                ->add('nuitee', 'text', array('data' => $lesLignesFrais[3], 'label' => 'Nuitée', 'attr' => array('pattern'=>'[0-9]+', 'title'=>'Nombre entier supérieur à 0')))
                ->add('etape', 'text', array('data' => $lesLignesFrais[1], 'attr' => array('pattern'=>'[0-9]+', 'title'=>'Nombre entier supérieur à 0')))
                ->add('km', 'text', array('data' => $lesLignesFrais[2], 'attr' => array('pattern'=>'[0-9]+', 'title'=>'Nombre entier supérieur à 0')))
                ->add('etat', 'choice', array('choices' => $lesEtat, 'data' => $ficheFrais->getIdetatfraisforfait()->getIdetat(), 'attr' => array('class'=>'form-control')))
                ->add('valider', 'submit', array('label' => 'Mettre à jour', 'attr' => array('class' => 'btn btn-primary')))
                ->add('annuler', 'reset', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-danger')))
                ->getForm() ;
        $formFraisForfait->handleRequest($request);

                // ------------------------------------------------------------------------ //
        
        $formHorsClassification = $this->createFormBuilder()
                ->add('justificatif', 'text', array('data' => $ficheFrais->getNbjustificatifs(), 'label' =>'Nombre justificatif', 'attr' => array('pattern'=>'[0-9]+', 'title'=>'Nombre entier supérieur à 0')))
                ->add('montant', 'text', array('data' => $ficheFrais->getMontantvalide(), 'label' => 'Montant total', 'attr' => array('pattern'=>'[-+]?[0-9]+(\.[0-9]+)?', 'title'=>'Nombres réels supérieur à 0'))) 
                ->add('etat', 'choice', array('choices' => $lesEtat, 'data' => $ficheFrais->getIdetatfraishorsclassification()->getIdetat(), 'attr' => array('class'=>'form-control')))
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
            
            $repository_etat = $em->getRepository('mehGsbBundle:etat');
            $etat = $repository_etat->find($formFraisForfait['etat']->getData());
            $ficheFrais->setIdetatfraisforfait($etat);
            
            $em->persist($ficheFrais);
            $em->flush();
            return $this->redirectToRoute('pageValiderFraisDunVisiteur', array('idFiche' => $idFiche));
        }
        else if($formHorsClassification->isValid()){
            
            $ficheFrais->setNbjustificatifs($formHorsClassification['justificatif']->getData());
            $ficheFrais->setMontantvalide($formHorsClassification['montant']->getData());
            $ficheFrais->setDatemodif(date_create(date('Y-m-d')));
            
            $repository_etat = $em->getRepository('mehGsbBundle:etat');
            $etat = $repository_etat->find($formHorsClassification['etat']->getData());
            $ficheFrais->setIdetatfraishorsclassification($etat);
            
            $em->persist($ficheFrais);
            $em->flush();
                
            return $this->redirectToRoute('pageValiderFraisDunVisiteur', array('idFiche' => $idFiche));
        }
       
        return $this->render('mehGsbBundle:Comptable:VueValiderFraisDunVisiteur.html.twig', array('ficheFrais' => $ficheFrais, 'idFiche' => $idFiche, 'visiteur' => $visiteur, 'lesHorsForfait' => $lesHorsForfait, 'formFraisForfait' => $formFraisForfait->createView(), 'formHorsClassification' => $formHorsClassification->createView())); 
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
    
    
    public function modifierEtatHorsForfaitAction($idFiche, $idEtat, $idHorsForfait) {
        
        $em = $this->getDoctrine()->getManager();
        
        $repository_etat = $em->getRepository('mehGsbBundle:Etat');
        $etat = $repository_etat->find($idEtat);
        
        $repository_horsForfait = $em->getRepository('mehGsbBundle:Lignefraishorsforfait');
        $horsForfait = $repository_horsForfait->find($idHorsForfait);
        
        $horsForfait->setIdetat($etat);
        $em->persist($horsForfait);
        $em->flush();
        
        return $this->redirectToRoute('pageValiderFraisDunVisiteur', array('idFiche' => $idFiche));
    }
    
    public function modifierLibelleHorsForfaitAction($idFiche, $idHorsForfait, $btn) {
        
        $em = $this->getDoctrine()->getManager();
        
        $repository_horsForfait = $em->getRepository('mehGsbBundle:Lignefraishorsforfait');
        $horsForfait = $repository_horsForfait->find($idHorsForfait);
        
        if($btn == 1){
            $libelleActuel = $horsForfait->getLibelle();
            $horsForfait->setLibelle("REFUSE ".$libelleActuel);
            $horsForfait->setRefuser(true);
        }
        else if($btn == 2){
            $libelleActuel = $horsForfait->getLibelle();
            $horsForfait->setLibelle(substr($libelleActuel, 7));
            $horsForfait->setRefuser(false);
        }
        $em->persist($horsForfait);
        $em->flush();
        return $this->redirectToRoute('pageValiderFraisDunVisiteur', array('idFiche' => $idFiche));
    }
    
    public function validerFicheAction($idFiche) {
        
        $em = $this->getDoctrine()->getManager();
        
        $repository_ficheFais = $em->getRepository('mehGsbBundle:Fichefrais');
        $ficheFrais = $repository_ficheFais->find($idFiche);
        
        $repository_etat = $em->getRepository('mehGsbBundle:Etat');
        $etatValider = $repository_etat->find(3);
        
        $ficheFrais->setIdetatfiche($etatValider);
        $em->persist($ficheFrais);
        $em->flush();
        
        return $this->redirectToRoute('pageValiderFraisDunVisiteur', array('idFiche' => $idFiche));
    }
        
       
}
