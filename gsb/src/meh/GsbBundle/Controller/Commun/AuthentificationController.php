<?php

namespace meh\GsbBundle\Controller\Commun;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AuthentificationController extends Controller
{
    public function authentificationAction()
    {

        $formAuthentification = $this->createFormBuilder()
                ->add('login', 'text', array('attr' => array('placeholder' => 'Nom d\'utilisateur', 'class' => 'form-control')))
                ->add('mdp', 'password', array('attr' => array('placeholder' => 'Mot de passe', 'class' => 'form-control')))
                ->add('valider', 'submit', array('label' => 'S\'authentifier', 'attr' => array('class' => 'btn btn-primary')))
                ->add('annuler', 'reset', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-danger')))
                ->getForm() ;
        
        $request = $this->container->get('request') ;
        $formAuthentification->handleRequest($request);
        $erreurNdc = "";
        $erreurMdp = "";
        
        if($formAuthentification->isValid()){
            
            $em = $this->getDoctrine()->getManager() ;
            $lesVisiteurs = $em->getRepository('mehGsbBundle:Visiteur') ; // tout les visiteurs
            $lesComptables = $em->getRepository('mehGsbBundle:Comptable'); // tout les comptables
            
            $login = $formAuthentification['login']->getData() ;
            $mdp = $formAuthentification['mdp']->getData() ;
            
            $visiteur = $lesVisiteurs->findOneBy(array('login' => $login));
            $comptable = $lesComptables->findOneBy(array('login' => $login));
            
            if($visiteur || $comptable){ //si ce compte existe chez un visiteur ou comptable
                
                if( $visiteur != null && $visiteur->getMdp() == md5($mdp)){ 
                    
                    $request->getSession()->set('idVisiteur', $visiteur->getIdVisiteur());
                    $request->getSession()->set('nom', $visiteur->getNom());
                    $request->getSession()->set('prenom', $visiteur->getPrenom());
                    $request->getSession()->set('login', $visiteur->getLogin());
                    $request->getSession()->set('mdp', $visiteur->getMdp());
                    $request->getSession()->set('profil', 'visiteur');
                    $request->getSession()->set('menuVisiteur', "activer");
                    
                    return $this->redirectToRoute('pageAccueil');
                }  
                else if( $comptable != null && $comptable->getMdp() == md5($mdp)){
                    
                    $request->getSession()->set('idComptable', $comptable->getIdComptable());
                    $request->getSession()->set('nom', $comptable->getNom());
                    $request->getSession()->set('prenom', $comptable->getPrenom());
                    $request->getSession()->set('login', $comptable->getLogin());
                    $request->getSession()->set('mdp', $comptable->getMdp());
                    $request->getSession()->set('profil', 'comptable');
                    $request->getSession()->set('menuComptable', "activer");
                    
                    return $this->redirectToRoute('pageAccueil');
                    
                }
                else{
                    $erreurMdp = "Le mot de passe est incorrect";
                    return $this->render('mehGsbBundle:Commun:VueAuthentification.html.twig', array('formAuthentification' => $formAuthentification->createView(), 'erreurNdc' => $erreurNdc, 'erreurMdp' => $erreurMdp));     
                }
            }
            else{ //si ce compte existe pas chez un visiteur ou comptable
                $erreurNdc = "Cet utilisateur n'existe pas";
                return $this->render('mehGsbBundle:Commun:VueAuthentification.html.twig', array('formAuthentification' => $formAuthentification->createView(), 'erreurNdc' => $erreurNdc, 'erreurMdp' => $erreurMdp)); 
            }
        }  
        return $this->render('mehGsbBundle:Commun:VueAuthentification.html.twig', array('formAuthentification' => $formAuthentification->createView(), 'erreurNdc' => $erreurNdc, 'erreurMdp' => $erreurMdp)); 
    }
    
}

