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
        
        $notif = new Notification;
        $notif->setComment($comment);
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
            array("destinataire" => $user),
            array("date" => "desc")
        );
        
        $notifTab = [];
        
        foreach($listNotif as $notif)
        {
            if(empty($notifTab))
            {
                $notifObject = ["object" => $notif, "nb" => 1, "liste" => serialize([$notif->getId()])];
                array_push($notifTab, $notifObject);
                continue;
            }
            
            $nbNotif = count($notifTab);

            for($i = 0; $i < $nbNotif; $i++)
            {
                if($notif->getType() == $notifTab[$i]["object"]->getType() && $notif->getProblem() == $notifTab[$i]["object"]->getProblem())
                {
                    if($notif->getType() == "com-reply-add")
                    {
                        if($notif->getComment()->getComFrom() == $notifTab[$i]["object"]->getComment()->getComFrom())
                        {
                            $notifTab[$i]["nb"] = $notifTab[$i]["nb"] + 1;
                            $liste = unserialize($notifTab[$i]["liste"]);
                            array_push($liste, $notif->getId());
                            $notifTab[$i]["liste"] = serialize($liste);
                            break;
                        }
                    }
                    else
                    {
                        $notifTab[$i]["nb"] = $notifTab[$i]["nb"] + 1;
                        $liste = unserialize($notifTab[$i]["liste"]);
                        array_push($liste, $notif->getId());
                        $notifTab[$i]["liste"] = serialize($liste);
                        break;
                    }
                }
                
                if($i == count($notifTab) - 1)
                {
                    $notifObject = ["object" => $notif, "nb" => 1, "liste" => serialize([$notif->getId()])];
                    array_push($notifTab, $notifObject);
                }
            }
        }
        
        return $this->render("SocialBundle:Notification:notification.html.twig", array(
            "listNotif" => $notifTab
        ));
    }
    
    
    public function openedNotificationAction($listeId = null, $clear = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $notifRep = $em->getRepository("SocialBundle:Notification");
            
        if($clear == 1)
        {
            $listNotif = $notifRep->findBy(
                array("destinataire" => $this->getUser())
            );
                
            foreach($listNotif as $clearNotif)
            {
                $em->remove($clearNotif);
            }
                
            $em->flush();
            return new Response("Notifications vidées");
        }
            
        $listNotif = unserialize($listeId);
        
        $notifToRemove = $notifRep->findBy(array("id" => $listNotif));
            
        foreach($notifToRemove as $notifRem)
        {
            if($notifRem->getDestinataire() == $this->getUser())
            {
                $em->remove($notifRem);
            }
        }
            
        $em->flush();
            
        return new Response("Notification ouverte");
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
                if($notif->getMultiple() <= 1)
                {
                    $em->remove($notif);
                }
                else
                {
                    $listeId = $notif->getListe();
                    $comKey = array_search($comment->getId(), $listeId);
                    array_splice($listeId, $comKey, 1);
                    $newCom = $em->getRepository("SocialBundle:Comment")->findOneBy(array("id" => $listeId[0]));
                    $notif->setListe($listeId);
                    $notif->setComment($newCom);
                    $notif->setMultiple($notif->getMultiple() -1);
                }
            }
            $em->flush();
            return new Response("Notification supprimée");
        }
    }
}
