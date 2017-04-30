<?php

namespace meh\GsbBundle\Controller\Commun;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use meh\GsbBundle\Modele\ModeleGsb;

class AccueilController extends Controller
{
    public function accueilAction()
    {
        $request = $this->container->get('request') ;
        
        if($request->getSession()->get('profil') != 'visiteur' && $request->getSession()->get('profil') != 'comptable'){
            return $this->redirectToRoute('pageAuthentification');
        }
        
        return $this->render('mehGsbBundle:Commun:VueAccueil.html.twig'); 
    }
    
    public function pageChangerMdpAction()
    {
        $formMdp = $this->createFormBuilder()
                ->add('mdpActuel', 'password', array('label' => 'Mot de passe actuel', 'attr' => array('autofocus' => 'autofocus')))
                ->add('mdp1', 'password', array('label' => 'Nouveau mot de passe'))
                ->add('mdp2', 'password', array('label' => 'Retaper le nouveau mot de passe'))
                ->add('valider', 'submit', array('label' => 'Modifier', 'attr' => array('class' => 'btn btn-primary')))
                ->getForm() ;
        
        $em = $this->getDoctrine()->getManager();
        
        $request = $this->container->get('request') ;
        $formMdp->handleRequest($request);
        
        $erreurMdpActuel = "";
        $erreurNewMdp = "";
        $succes = "";
        
        if($formMdp->isValid()){
            if($request->getSession()->get('profil') == 'visiteur'){
                $repository_visiteur = $em->getRepository('mehGsbBundle:Visiteur');
                $visiteur = $repository_visiteur->find($request->getSession()->get('idVisiteur'));
                if($visiteur->getMdp() == md5($formMdp['mdpActuel']->getData())){
                    if($formMdp['mdp1']->getData() == $formMdp['mdp2']->getData() ){
                        $visiteur->setMdp(md5($formMdp['mdp1']->getData()));
                        $em->persist($visiteur);
                        $em->flush();
                        $succes = "Votre mot de passe a bien été modifié.";
                        return $this->render('mehGsbBundle:Commun:VueChangerMdp.html.twig', array('formMdp' => $formMdp->createView(), 'erreurMdpActuel' => $erreurMdpActuel, 'erreurNewMdp' => $erreurNewMdp, 'succes' => $succes));
                    }else{
                        $erreurNewMdp = "Les deux nouveaux mot de passe ne sont pas identiques";
                        return $this->render('mehGsbBundle:Commun:VueChangerMdp.html.twig', array('formMdp' => $formMdp->createView(), 'erreurMdpActuel' => $erreurMdpActuel, 'erreurNewMdp' => $erreurNewMdp, 'succes' => $succes));
                    }
                }else{
                    $erreurMdpActuel = "Le mot de passe actuel est incorrect.";
                    return $this->render('mehGsbBundle:Commun:VueChangerMdp.html.twig', array('formMdp' => $formMdp->createView(), 'erreurMdpActuel' => $erreurMdpActuel, 'erreurNewMdp' => $erreurNewMdp, 'succes' => $succes));
                }
            }
            if($request->getSession()->get('profil') == 'comptable'){
                
                $repository_comptable = $em->getRepository('mehGsbBundle:Comptable');
                $comptable = $repository_comptable->find($request->getSession()->get('idComptable'));
                if($comptable->getMdp() == md5($formMdp['mdpActuel']->getData())){
                    if($formMdp['mdp1']->getData() == $formMdp['mdp2']->getData() ){
                        $comptable->setMdp(md5($formMdp['mdp1']->getData()));
                        $em->persist($comptable);
                        $em->flush();
                        $succes = "Votre mot de passe a bien été modifié.";
                        return $this->render('mehGsbBundle:Commun:VueChangerMdp.html.twig', array('formMdp' => $formMdp->createView(), 'erreurMdpActuel' => $erreurMdpActuel, 'erreurNewMdp' => $erreurNewMdp, 'succes' => $succes));
                    }else{
                        $erreurNewMdp = "Les deux nouveaux mot de passe ne sont pas identiques";
                        return $this->render('mehGsbBundle:Commun:VueChangerMdp.html.twig', array('formMdp' => $formMdp->createView(), 'erreurMdpActuel' => $erreurMdpActuel, 'erreurNewMdp' => $erreurNewMdp, 'succes' => $succes));
                    }
                }else{
                    $erreurMdpActuel = "Le mot de passe actuel est incorrect.";
                    return $this->render('mehGsbBundle:Commun:VueChangerMdp.html.twig', array('formMdp' => $formMdp->createView(), 'erreurMdpActuel' => $erreurMdpActuel, 'erreurNewMdp' => $erreurNewMdp, 'succes' => $succes));
                }
            }
        }
        return $this->render('mehGsbBundle:Commun:VueChangerMdp.html.twig', array('formMdp' => $formMdp->createView(), 'erreurMdpActuel' => $erreurMdpActuel, 'erreurNewMdp' => $erreurNewMdp, 'succes' => $succes));
    }
    
     public function deconnexionAction()
    {
        $request = $this->container->get('request') ;
        $request->getSession()->clear();
        return $this->redirectToRoute('pageAuthentification');
    }
    
}

