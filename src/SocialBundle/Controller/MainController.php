<?php

namespace SocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use SocialBundle\Entity\Problem;
use SocialBundle\Entity\Fichier;
use SocialBundle\Entity\Comment;
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
        $asFile = false;
        
        if(isset($_FILES['files']["name"][0]) && $_FILES['files']["name"][0] != "")
        {
            $files = $_FILES['files'];
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
                if($files["size"][$i] > 2097152 || $files["error"][$i] == 1)
                {
                    $this->get('session')->getFlashBag()->add('error', 'Le fichier ' . $files["name"][$i] . " dépasse la taille maximum de 2Mo");
                    $em->remove($problem);
                    $em->flush();
                    return $this->redirectToRoute("social_problem_add");
                }
                
                $pathName = $pbId . "." . $i . ".txt";

                if (!move_uploaded_file($files['tmp_name'][$i], "ressources/txt/" . $pathName))
                {
                    $em->remove($problem);
                    $em->flush();
                    return new Response("Erreur lors du téléchargement des fichiers");
                }
                
                $fichier = new Fichier;
                $fichier->setProblem($problem);
                $fichier->setName($files["name"][$i]);
                $fichier->setPathName($pathName);
                
                $em->persist($fichier);
            }
            $em->flush();
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
		$em = $this->getDoctrine()->getManager();
        $fichierRep = $em->getRepository("SocialBundle:Fichier");
		$comRep = $em->getRepository("SocialBundle:Comment");
		
        $listFichiers = $fichierRep->findBy(
            array("problem" => $problem, "comment" => null),
            array("pathName" => "asc")
        );
		
		$listComs = $comRep->findBy(
            array("problem" => $problem),
            array("date" => "desc")
        );
		
		$fichiersContent = [];
		$comsContent = [];
		
		foreach($listFichiers as $fichier)
		{
			array_push($fichiersContent, ["name" => $fichier->getName(), "content" => file_get_contents("ressources/txt/" . $fichier->getPathName())]);
		}
		
		foreach($listComs as $com)
		{
			if($com->getCorrection())
			{
				$editedCode = $fichierRep->findOneBy(array("comment" => $com));
				$editedCodeName = $editedCode->getName();
				$editedCodeContent = file_get_contents("ressources/txt/" . $editedCode->getPathName());
				
				array_push($comsContent, ["id" => $com->getId(), "author" => $com->getAuteur(), "content" => $com->getContenu(), "date" => $com->getDate(), "editedName" => $editedCodeName, "editedContent" => $editedCodeContent]);
			}
			else
			{
				array_push($comsContent, ["id" => $com->getId(), "author" => $com->getAuteur(), "content" => $com->getContenu(), "date" => $com->getDate(), "editedName" => null, "editedContent" => null]);
			}
		}
        
        return $this->render("SocialBundle:Main:problemPage.html.twig", array(
            "problem" => $problem,
			"fichiersContent" => $fichiersContent,
			"comsContent" => $comsContent
        ));
    }
    
    /**
     * @ParamConverter("problem", options={"mapping": {"problem_id": "id"}})
     */
    public function problemCommentAddAction(Problem $problem)
    {
        $request = Request::createFromGlobals();
        $content = ucfirst(htmlspecialchars($request->request->get('comment')));
        $hasCorrection = false;
        
        if(null !== $request->request->get('editedCodeName') && null !== $request->request->get('editedCodeContent'))
        {
            $correctionName = $request->request->get('editedCodeName');
            $correctionContent = str_replace('$quot;', '"', $request->request->get('editedCodeContent'));
            $hasCorrection = true;
        }
        
        $comment = new Comment;
        $comment->setAuteur($this->getUser());
        $comment->setContenu($content);
        $comment->setProblem($problem);
        
        if($hasCorrection)
        {
            $comment->setCorrection(true);
        }
		
		$validator = $this->get('validator');
        $listErrors = $validator->validate($comment);
        
        if(count($listErrors) > 0) {
            return new Response((string) $listErrors);
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();
        
        $commentId = $comment->getId();
        
        if($hasCorrection)
        {
            $correcPathName = "c." . $problem->getId() . "." . $commentId . ".txt";
            file_put_contents("ressources/txt/".$correcPathName, $correctionContent);
            $correction = new Fichier;
            $correction->setName($correctionName);
            $correction->setPathName($correcPathName);
            $correction->setProblem($problem);
            $correction->setComment($comment);
            
            $em->persist($correction);
            $em->flush();
        }
        
        return $this->redirectToRoute("social_problem_show", array("problem_titreSlug" => $problem->getTitreSlug()));
    }
	
	/**
     * @ParamConverter("comment", options={"mapping": {"comment_id": "id"}})
     */
	public function problemCommentRemoveAction(Comment $comment)
	{
		if($this->getUser() == $comment->getAuteur())
		{
			$em = $this->getDoctrine()->getManager();
			$problemSlug = $comment->getProblem()->getTitreSlug();
			
			if($comment->getCorrection())
			{
				$fileRep = $em->getRepository("SocialBundle:Fichier");
				$fichier = $fileRep->findOneBy(array("comment" => $comment));
				unlink("ressources/txt/" . $fichier->getPathName());
				$em->remove($fichier);
			}
			
			$em->remove($comment);
			$em->flush();
			
			return $this->redirectToRoute("social_problem_show", array("problem_titreSlug" => $problemSlug));
		}
		else
		{
			return new Response("Erreur lors de la supression du commentaire");
		}
	}
}
