<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{

    /**
     * @Route("/admin/moderate/question/{question}", methods="POST", name="moderate_question")
     */
    public function moderateQuestion(Question $question)
    {   

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

            $questionIsDisplay = $question->getIsDisplay();

            if ($questionIsDisplay) {
                $question->setIsDisplay('0');
            } else {
                $question->setIsDisplay('1');
            }

            
            $em = $this->getDoctrine()->getManager();
            $em->persist($question);
    
            $em->flush();

        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/admin/moderate/response/{response}", methods="POST", name="moderate_response")
     */
    public function moderateResponse(Response $response)
    {   

        $questionId = $response->getQuestion()->getId();

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

            $responseIsDisplay = $response->getIsDisplay();

            if ($responseIsDisplay) {
                $response->setIsDisplay('0');
            } else {
                $response->setIsDisplay('1');
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($response);
    
            $em->flush();

        return $this->redirectToRoute('show_question', ['id' => $questionId]);
    }
}
