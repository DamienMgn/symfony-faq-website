<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Question;
use App\Entity\Response;
use App\Form\QuestionType;
use App\Form\ResponseType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuestionResponseController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {   

        $userRoles = $this->getUser()->getRoles();

        if (in_array("ROLE_ADMIN", $userRoles)) {
            $questions = $this->getDoctrine()->getRepository(Question::class)->findAll();
        } else {
            $questions = $this->getDoctrine()->getRepository(Question::class)->findBy(
                ['isDisplay' => '1'],
                ['createdAt' => 'ASC']
            );
        }
        
        $tags = $this->getDoctrine()->getRepository(Tag::class)->findAll();

        return $this->render('question/index.html.twig', [
            'questions' => $questions,
            'allTags' => $tags
        ]);
    }

    /**
     * @Route("/question/{id}", name="show_question", requirements={"id":"\d+"})
     */
    public function showQuestion(Request $request, Question $question)
    {   
        $question = $this->getDoctrine()->getRepository(Question::class)->find($question);

        $response = new Response();

        $userRoles = $this->getUser()->getRoles();

        if (in_array("ROLE_ADMIN", $userRoles)) {
            $responses = $this->getDoctrine()->getRepository(Response::class)->findBy(['question' => $question]);
        } else {
            $responses = $this->getDoctrine()->getRepository(Response::class)->findByResponsesDisplay('1', $question);
        }

        $form = $this->createForm(ResponseType::class, $response);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $response = $form->getData();
            $response->setQuestion($question);

            $user = $this->getUser();
            $response->setUser($user);
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($response);
            $entityManager->flush();
    
            return $this->redirectToRoute('show_question', ['id' => $question->getId()]);
        }

        return $this->render('question/show_question.html.twig', [
            'question' => $question,
            'responseForm' => $form->createView(),
            'responses' => $responses
        ]);
    }
    
    /**
     * @Route("/question/add", name="add_question")
     */
    public function addQuestion(Request $request)
    {   

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $question = new Question();

        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $question = $form->getData();

            $user = $this->getUser();
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

    /**
     * @Route("/question/select_response/{response}/{question}", methods={"POST"}, name="select_response")
     */
    public function selectResponse(Question $question, Response $response)
    {   
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $userSecurity = $this->getUser()->getId();
        $userQuestion = $question->getUser()->getId();

        if ($userSecurity === $userQuestion) {
            $question->setCorrectResponse($response);

            $em = $this->getDoctrine()->getManager();
            $em->persist($question);
    
            $em->flush();
        }

        return $this->redirectToRoute('show_question', ['id' => $question->getId()]);
    }
}
