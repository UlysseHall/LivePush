<?php

namespace SocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use SocialBundle\Entity\Problem;
use SocialBundle\Entity\Fichier;
use SocialBundle\Entity\Comment;
use SocialBundle\Entity\Notification;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class NotificationController extends Controller
{
    public function addNotificationAction($problem, $comment = null, $type)
    {
        $em = $this->getDoctrine()->getManager();
        $notifRep = $em->getRepository("SocialBundle:Notification");
        $expediteur = $this->getUser();
        
        switch($type)
        {
            case "com-add":
                $destinataire = $problem->getAuteur();
                break;
            
            case "com-reply-add":
                $destinataire = $comment->getComFrom()->getAuteur();
                break;
                
            case "problem-solved-with-com":
                $destinataire = $comment->getAuteur();
                break;
        }
        
        $listNotif = $notifRep->findBy(
            array("destinataire" => $destinataire, "ouvert" => false, "type" => $type, "problem" => $problem)
        );
        
        if(count($listNotif) > 0)
        {
            $flush = 0;
            
            foreach($listNotif as $sameNotif)
            {
                if(($type == "com-add") || (!is_null($sameNotif->getComment()->getComFrom()) && $sameNotif->getComment()->getComFrom() == $comment->getComFrom()))
                {
                    $sameNotif->setMultiple($sameNotif->getMultiple() + 1);
                    $listeId = $sameNotif->getListe();
                    array_push($listeId, $comment->getId());
                    $sameNotif->setListe($listeId);
                    $flush = 1;
                }
            }
            
            if($flush == 1)
            {
                $em->flush();
                return new Response("sent");
            }
        }
        
        $notif = new Notification;
        $notif->setComment($comment);
        $notif->setListe(array($comment->getId()));
		$notif->setExpediteur($expediteur);
		$notif->setDestinataire($destinataire);
		$notif->setProblem($problem);
		$notif->setType($type);
		
		$em->persist($notif);
		$em->flush();
		
		return new Response("sent");
    }
    
    public function getNotificationAction()
    {
        $em = $this->getDoctrine()->getManager();
        $notifRep = $em->getRepository("SocialBundle:Notification");
        $user = $this->getUser();
        
        $listNotif = $notifRep->findBy(
            array("destinataire" => $user, "ouvert" => false),
            array("date" => "desc")
        );
        
        return $this->render("SocialBundle:Notification:notification.html.twig", array(
            "listNotif" => $listNotif
        ));
    }
    
    /**
     * @ParamConverter("notif", options={"mapping": {"notification_id": "id"}})
     */
    public function openedNotificationAction(Notification $notif = null, $clear = 0)
    {
        $em = $this->getDoctrine()->getManager();
            
        if($clear == 1)
        {
            $notifRep = $em->getRepository("SocialBundle:Notification");
            $listNotif = $notifRep->findBy(
                array("destinataire" => $this->getUser(), "ouvert" => false)
            );
                
            foreach($listNotif as $clearNotif)
            {
                $clearNotif->setOuvert(true);
            }
                
            $em->flush();
            return new Response("Notifications vidées");
        }
            
        if($this->getUser() == $notif->getDestinataire())
        {
            $notif->setOuvert(true);
            $em->flush();
            
            return new Response("Notification ouverte");
        }
        
        return new Response("Erreur d'authentification");
    }
    
    public function removeNotificationAction(Problem $problem = null, Comment $comment = null)
    {
        $em = $this->getDoctrine()->getManager();
        $notifRep = $em->getRepository("SocialBundle:Notification");
        
        if(!is_null($problem))
        {
            $notifList = $notifRep->findBy(array("problem" => $problem));
            foreach($notifList as $notif)
            {
                $em->remove($notif);
            }
            $em->flush();
            return new Response("Notification supprimée");
        }
        
        if(!is_null($comment))
        {
            $notifList = $notifRep->findBy(array("comment" => $comment));
            foreach($notifList as $notif)
            {
                if($notif->getMultiple <= 1)
                {
                    $em->remove($notif);
                }
                else
                {
                    $notif->setMultiple($notif->getMultiple() -1);
                }
            }
            $em->flush();
            return new Response("Notification supprimée");
        }
    }
}
