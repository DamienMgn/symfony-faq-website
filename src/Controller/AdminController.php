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
        $questionIsDisplay = $question->getIsDisplay() ? $question->setIsDisplay('0') : $question->setIsDisplay('1');

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

        $responseIsDisplay = $response->getIsDisplay() ? $response->setIsDisplay('0') : $response->setIsDisplay('1');

        $em = $this->getDoctrine()->getManager();
        $em->persist($response);

        $em->flush();

        return $this->redirectToRoute('show_question', ['id' => $questionId]);
    }
}
