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
                $destinataire = $comment->getAuteur();
                break;
                
            case "problem-solved-with-com":
                $destinataire = $comment->getAuteur();
                break;
        }
        
        $listNotif = $notifRep->findBy(
            array("destinataire" => $destinataire, "ouvert" => false, "type" => $type, "problem" => $problem, "comment" => $comment)
        );
        
        if(count($listNotif) > 0)
        {
            foreach($listNotif as $sameNotif)
            {
                $sameNotif->setMultiple($sameNotif->getMultiple() + 1);
            }
            
            $em->flush();
            return new Response("sent");
        }
        
        $notif = new Notification;
        
        if($type == "com-reply-add" || $type == "problem-solved-with-com")
        {
            $notif->setComment($comment);
        }
		
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
    public function openedNotificationAction(Notification $notif)
    {
        if($this->getUser() == $notif->getDestinataire())
        {
            $notif->setOuvert(true);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            return new Response("Notification ouverte");
        }
        
        return new Response("Erreur d'authentification");
    }
}
