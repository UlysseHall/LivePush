<?php

namespace SocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SocialBundle\Entity\Feedback;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class FeedbackController extends Controller
{
    public function sendFeedbackAction()
    {
        $request = Request::createFromGlobals();
        
        $type = htmlspecialchars($request->request->get('type'));
        $message = htmlspecialchars($request->request->get('message'));
        
        $em = $this->getDoctrine()->getManager();
        $fbRep = $em->getRepository("SocialBundle:Feedback");
        
        $feedback = new Feedback;
        $feedback->setType($type);
        $feedback->setMessage($message);
        $feedback->setAuteur($this->getUser());
        
        $em->persist($feedback);
        $em->flush();
        
        return $this->redirectToRoute("social_home");
    }
    
    public function getFeedbackAction()
    {
        if($this->getUser()->getEmail() == "ulysse.hall@hetic.net")
        {
            $em = $this->getDoctrine()->getManager();
            $fbRep = $em->getRepository("SocialBundle:Feedback");
            
            $listFb = $fbRep->findBy(array(), array('date' => 'desc'));
            
            return $this->render("SocialBundle:Main:feedbackList.html.twig", array(
            "listFb" => $listFb
        ));
        }
        else
        {
            return new Response("Zone réservée aux admins");
        }
    }
}
