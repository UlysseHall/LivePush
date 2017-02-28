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
		$contenu = "test";
		
        $notif = new Notification;
		$notif->setExpediteur($this->getUser());
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
