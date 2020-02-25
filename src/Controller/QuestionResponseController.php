<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Question;
use App\Entity\Response;
use App\Form\QuestionType;
use App\Form\ResponseType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuestionResponseController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {   
        if (!is_null($this->getUser()) && in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            $query = $this->getDoctrine()->getRepository(Question::class)->findAllQuestionJoinTags();
        } else {
            $query = $this->getDoctrine()->getRepository(Question::class)->findAllQuestions();
        }

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            7 /*limit per page*/
        );
        
        $tags = $this->getDoctrine()->getRepository(Tag::class)->findAll();

        return $this->render('question/index.html.twig', [
            'questions' => $pagination,
            'allTags' => $tags
        ]);
    }

    /**
     * @Route("/question/{id}", name="show_question", requirements={"id":"\d+"})
     */
    public function showQuestion(Request $request, Question $question)
    {   
        $response = new Response();

        if (!is_null($this->getUser()) && in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            $responses = $this->getDoctrine()->getRepository(Response::class)->findBy(
                ['question' => $question],
                ['createdAt' => 'ASC']
            );
        } else {
            if (!$question->getIsDisplay()) {
                throw $this->createNotFoundException('La question n\'existe pas');
            }
            $responses = $this->getDoctrine()->getRepository(Response::class)->findByResponsesDisplay($question);
        }

        $form = $this->createForm(ResponseType::class, $response);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $response = $form->getData();
            $response->setQuestion($question);

            $user = $this->getUser();
            $response->setUser($user);
    
            $em = $this->getDoctrine()->getManager();
            $em->persist($response);
            $em->flush();

            $this->addFlash(
                'notice',
                'Votre réponse a été ajoutée !'
            );
    
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
        $question = new Question();

        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $question = $form->getData();

            $user = $this->getUser();
            $question->setUser($user);
    
            $em = $this->getDoctrine()->getManager();
            $em->persist($question);
            $em->flush();

            $this->addFlash(
                'notice',
                'Votre question a été ajoutée !'
            );
    
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

        if ($this->getUser() === $question->getUser()) {
            $question->setCorrectResponse($response);

            $em = $this->getDoctrine()->getManager();
            $em->persist($question);
    
            $em->flush();
        }

        return $this->redirectToRoute('show_question', ['id' => $question->getId()]);
    }
    
    /**
     * @Route("/question/search/", name="search_question")
     */
    public function searchResponse(Request $request, PaginatorInterface $paginator)
    {   
        $search = $request->query->get('search-input');

        if (!is_null($this->getUser()) && in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            $query = $this->getDoctrine()->getRepository(Question::class)->findByString($search);
        } else {
            $query = $this->getDoctrine()->getRepository(Question::class)->findByStringOnlyValidateQuestion($search);
        }

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            7 /*limit per page*/
        );

        $tags = $this->getDoctrine()->getRepository(Tag::class)->findAll();

        return $this->render('question/index.html.twig', [
            'questions' => $pagination,
            'allTags' => $tags
        ]);

    }
}
