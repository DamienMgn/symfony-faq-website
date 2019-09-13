<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Question;
use App\Entity\Response;
use App\Form\QuestionType;
use App\Form\ResponseType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
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
     * @Route("/question/{id}", name="show_question", requirements={"id":"\d+"})
     */
    public function showQuestion(Request $request, Question $question)
    {   
        $question = $this->getDoctrine()->getRepository(Question::class)->find($question);

        $response = new Response();

        $form = $this->createForm(ResponseType::class, $response);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $response = $form->getData();
            $response->setQuestion($question);
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($response);
            $entityManager->flush();
    
            return $this->redirectToRoute('show_question', ['id' => $question->getId()]);
        }

        return $this->render('question/show_question.html.twig', [
            'question' => $question,
            'responseForm' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/question/add", name="add_question")
     */
    public function addQuestion(Request $request, Security $security)
    {   
        $question = new Question();

        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $question = $form->getData();

            $user = $security->getUser();
            $question->setUser($user);
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->flush();
    
            return $this->redirectToRoute('show_question', ['id' => $question->getId()]);
        }

        return $this->render('question/add_question.html.twig', [
            'questionForm' => $form->createView(),
        ]);
    }
}
