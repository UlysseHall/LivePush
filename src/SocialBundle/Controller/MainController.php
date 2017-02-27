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
		$upFiles = json_decode($request->request->get('upFiles'), true);
        $newUpFiles = [];
        $asFile = false;
        
        if(isset($upFiles) && !empty($upFiles))
        {
            foreach($upFiles as $upFile)
            {
                if($upFile !== "deleted")
                {
                    array_push($newUpFiles, $upFile);
                    $asFile = true;
                }
            }
        }
        
        if($asFile && count($newUpFiles) > 6)
        {
            return new Response("Nombre de fichiers dépassé");
        }
        
        $problem = new Problem;
        $problem->setTitre($title);
        $problem->setContenu($content);
        $problem->setLangage($langage);
        $problem->setAuteur($this->getUser());
        if($asFile) { $problem->setNbFiles(count($newUpFiles)); }
        
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
            foreach($newUpFiles as $key=>$file)
            {
                if($file["size"] > 5242880)
                {
                    $this->get('session')->getFlashBag()->add('error', 'Le fichier ' . $file["name"] . " dépasse la taille maximum de 5Mo");
                    $em->remove($problem);
                    $em->flush();
                    return $this->redirectToRoute("social_problem_add");
                }
                
                if($file["image"])
                {
                    $dataImg = explode(',', $file["content"]);
                    $decodedImg = base64_decode($dataImg[1]);
                    $realImg = imagecreatefromstring($decodedImg);
                    
                    if($realImg !== false)
                    {
                        $pathName = $pbId . "." . $key . ".jpeg";
                        $uploaded = imagejpeg($realImg, "ressources/txt/" . $pathName);
                    }
                }
                else
                {
                    $pathName = $pbId . "." . $key . ".txt";
                    $uploaded = file_put_contents("ressources/txt/" . $pathName, utf8_encode(base64_decode($file["content"])));
                }
                
                if(!isset($uploaded) || $uploaded == false)
                {
                    $em->remove($problem);
                    $em->flush();
                    return new Response("Erreur lors du téléchargement des fichiers");
                }
                
                $fichier = new Fichier;
                $fichier->setProblem($problem);
                $fichier->setName($file["name"]);
                $fichier->setPathName($pathName);
                
                if($file["image"])
                {
                    $fichier->setImage(true);
                }
                
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
            array("problem" => $problem, "comFrom" => null),
            array("date" => "desc")
        );
		
		$listRepComs = $comRep->findResponses($problem);
		
		$fichiersContent = [];
		$comsContent = [];
		
		foreach($listFichiers as $fichier)
		{
            if($fichier->getImage() == false)
            {
                $fileContent = file_get_contents("ressources/txt/" . $fichier->getPathName());
            }
            else
            {
                $fileContent = $fichier->getPathName();
            }
			array_push($fichiersContent, ["name" => $fichier->getName(), "content" => $fileContent, "image" => $fichier->getImage()]);
		}
		
		foreach($listComs as $com)
		{
            $editedCodeName = null;
            $editedCodeContent = null;
            
			if($com->getCorrection())
			{
				$editedCode = $fichierRep->findOneBy(array("comment" => $com));
				$editedCodeName = $editedCode->getName();
				$editedCodeContent = file_get_contents("ressources/txt/" . $editedCode->getPathName());
            }
            
            $comObject = ["object" => $com, "editedName" => $editedCodeName, "editedContent" => $editedCodeContent];
            
            if($com->getHasResponse())
            {
				$comResponses = [];
				
                foreach($listRepComs as $repCom)
                {
                    if($repCom->getComFrom()->getId() == $com->getId())
                    {
                        array_push($comResponses, $repCom);
                    }
                }
				
				$comObject["responses"] = $comResponses;
            }
			
            if($com->getSolution())
            {
                array_unshift($comsContent, $comObject);
            }
			else
			{
				array_push($comsContent, $comObject);
			}
		}

        return $this->render("SocialBundle:Main:problemPage.html.twig", array(
            "problem" => $problem,
			"fichiersContent" => $fichiersContent,
			"comsContent" => $comsContent
        ));
    }
	
	/**
     * @ParamConverter("problem", options={"mapping": {"problem_titreSlug": "titreSlug"}})
     */
	public function problemRemoveAction(Problem $problem)
	{
		if($this->getUser() == $problem->getAuteur())
		{
			$em = $this->getDoctrine()->getManager();
			$comRep = $em->getRepository("SocialBundle:Comment");
			$fileRep = $em->getRepository("SocialBundle:Fichier");
			$listCom = $comRep->findBy(array("problem" => $problem));
			
			foreach($listCom as $com)
			{
				if($com->getCorrection())
				{
					$fichier = $fileRep->findOneBy(array("comment" => $com));
					unlink("ressources/txt/" . $fichier->getPathName());
					$em->remove($fichier);
				}
				$em->remove($com);
				$em->flush();
			}
			
			$listFiles = $fileRep->findBy(array("problem" => $problem));
			
			foreach($listFiles as $file)
			{
				unlink("ressources/txt/" . $file->getPathName());
				$em->remove($file);
			}
			
			$em->remove($problem);
			$em->flush();
			
			return $this->redirectToRoute("social_home");
		}
		else
		{
			return new Response("Erreur lors de la supression du problème");
		}
	}
	
	/**
     * @ParamConverter("problem", options={"mapping": {"problem_titreSlug": "titreSlug"}})
	 * @ParamConverter("comment", options={"mapping": {"comment_id": "id"}})
     */
	public function problemSolvedAction(Problem $problem, Comment $comment = null)
	{
		if($this->getUser() == $problem->getAuteur())
		{
			$em = $this->getDoctrine()->getManager();
			$problem->setResolu(true);
			
			if(!is_null($comment))
			{
				$comment->setSolution(true);
			}
			
			$em->flush();
			
			return $this->redirectToRoute("social_problem_show", array("problem_titreSlug" => $problem->getTitreSlug()));
		}
		else
		{
			return new Response("Erreur lors de la résolution du problème");
		}
	}
    
    /**
     * @ParamConverter("problem", options={"mapping": {"problem_id": "id"}})
     */
    public function problemCommentAddAction(Problem $problem, $comFromId)
    {
        $request = Request::createFromGlobals();
        $content = ucfirst(htmlspecialchars($request->request->get('comment')));
        $hasCorrection = false;
        
        if(null !== $request->request->get('editedCodeName') && null !== $request->request->get('editedCodeContent'))
        {
            $correctionName = $request->request->get('editedCodeName');
            $correctionContent = utf8_encode(base64_decode($request->request->get('editedCodeContent')));
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
        
        $em = $this->getDoctrine()->getManager();
        
        if($comFromId != -1)
        {
            $comRep = $em->getRepository("SocialBundle:Comment");
            $comFrom = $comRep->find($comFromId);
            $comFrom->setHasResponse(true);
            $comment->setComFrom($comFrom);
        }
		
		$validator = $this->get('validator');
        $listErrors = $validator->validate($comment);
        
        if(count($listErrors) > 0) {
            return new Response((string) $listErrors);
        }
        
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
			$comRep = $em->getRepository("SocialBundle:Comment");
			$listRepCom = $comRep->findBy(array("comFrom" => $comment));
			
			if($comment->getCorrection())
			{
				$fileRep = $em->getRepository("SocialBundle:Fichier");
				$fichier = $fileRep->findOneBy(array("comment" => $comment));
				unlink("ressources/txt/" . $fichier->getPathName());
				$em->remove($fichier);
			}
			
			foreach($listRepCom as $repCom)
			{
				$em->remove($repCom);
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
