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
    public function addNotificationAction($problem, $comment, $type)
    {
        $notif = new Notification;
        $expediteur = $this->getUser();
        
        switch($type)
        {
            case "com-add":
                $contenu = ucfirst($expediteur->getUsername()) . " a commenté votre problème " . $problem->getTitre();
                break;
            
            case "com-reply-add":
                $contenu = ucfirst($expediteur->getUsername()) . " a répondu à votre commentaire " . $comment->getContenu();
                $notif->setComment($comment);
                break;
                
            case "problem-solved-with-com":
                $contenu = "Votre solution à été validée sur le problème " . $problem->getTitre();
                $notif->setComment($comment);
                break;
        }
		
		$notif->setExpediteur($expediteur);
		$notif->setDestinataire($problem->getAuteur());
		$notif->setProblem($problem);
		$notif->setType($type);
		$notif->setContenu($contenu);
		
		$em = $this->getDoctrine()->getManager();
		$em->persist($notif);
		$em->flush();
		
		return new Response("sent");
    }
}
