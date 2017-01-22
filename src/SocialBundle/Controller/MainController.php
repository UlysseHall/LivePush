<?php

namespace SocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use SocialBundle\Entity\Problem;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
        $files = $_FILES['files'];
        $asFile = false;
        
        if($files["name"][0] != "" && isset($files["name"][0]))
        {
            $asFile = true;
        }
        
        if($asFile && count($files['name']) > 6)
        {
            return new Response("Nombre de fichiers dépassé");
        }
        
        $problem = new Problem;
        $problem->setTitre($title);
        $problem->setContenu($content);
        $problem->setLangage($langage);
        $problem->setAuteur($this->getUser());
        if($asFile) { $problem->setNbFiles(count($files['name'])); }
        
        $validator = $this->get('validator');
        $listErrors = $validator->validate($problem);
        
        if(count($listErrors) > 0) {
            return new Response((string) $listErrors);
        }
        
		$em = $this->getDoctrine()->getManager();
		$em->persist($problem);
		$em->flush();
        
        $pbId = $problem->getId();
        
        if($asFile)
        {
            for($i = 0; $i < count($files['name']); $i++)
            {
                if (!move_uploaded_file($files['tmp_name'][$i], "ressources/txt/" . $pbId . "." . $i . ".txt"))
                {
                    $em->remove($problem);
                    $em->flush();
                    return new Response("Erreur lors du téléchargement des fichiers");
                }
            }
        }
		
		return $this->redirectToRoute("social_problem_show", array("problem_titreSlug" => $problem->getTitreSlug()));
    }
    
    public function problemsGetAction()
    {
        $pbRep = $this->getDoctrine()->getManager()->getRepository("SocialBundle:Problem");
        $listProblems = $pbRep->findBy(array(), array('date' => 'desc'));
        
        return $this->render("SocialBundle:Main:problemDisplay.html.twig", array(
            "listProblems" => $listProblems
        ));
    }
    
    /**
     * @ParamConverter("problem", options={"mapping": {"problem_titreSlug": "titreSlug"}})
     */
    public function problemShowAction(Problem $problem)
    {
        return $this->render("SocialBundle:Main:problemPage.html.twig", array(
            "problem" => $problem
        ));
    }
}
