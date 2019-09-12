<?php

namespace App\Controller;

use App\Entity\Question;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuestionController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {   
        $questions = $this->getDoctrine()->getRepository(Question::class)->findAll();

        return $this->render('question/index.html.twig', [
            'questions' => $questions,
        ]);
    }

    /**
     * @Route("/question/{id}", name="show_question")
     */
    public function showQuestion(Question $question)
    {   
        $question = $this->getDoctrine()->getRepository(Question::class)->find($question);

        return $this->render('question/show_question.html.twig', [
            'question' => $question,
        ]);
    }
}
