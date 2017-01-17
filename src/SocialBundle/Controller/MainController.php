<?php

namespace SocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use SocialBundle\Entity\Problem;
use Symfony\Component\HttpFoundation\Response;

class MainController extends Controller
{
    public function homeAction()
    {
        return $this->render('SocialBundle:Main:home.html.twig');
    }

	public function problemAddAction()
	{
		return $this->render('SocialBundle:Main:problemAdd.html.twig');
	}
    
    public function problemAddUploadAction()
    {
        $request = Request::createFromGlobals();
        
        $title = ucfirst(htmlspecialchars($request->request->get('pbAddTitle')));
        $content = ucfirst(htmlspecialchars($request->request->get('pbAddContent')));
        $langage = htmlspecialchars($request->request->get('pbAddLangage'));
        
        $problem = new Problem;
        $problem->setTitre($title);
        $problem->setContenu($content);
        $problem->setLangage($langage);
        $problem->setAuteur($this->getUser());
        
        $validator = $this->get('validator');
        $listErrors = $validator->validate($problem);
        
        if(count($listErrors) > 0) {
            return new Response((string) $listErrors);
        }
        
		$em = $this->getDoctrine()->getManager();
		$em->persist($problem);
		$em->flush();
		
		return $this->redirectToRoute("social_home");
    }
}
