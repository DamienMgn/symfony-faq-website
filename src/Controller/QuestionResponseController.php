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

        if(!is_null($this->getUser())) {
            $userRoles = $this->getUser()->getRoles();
        } else {
            $userRoles = [];
        }

        if (in_array("ROLE_ADMIN", $userRoles)) {
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
        $question = $this->getDoctrine()->getRepository(Question::class)->find($question);

        $response = new Response();

        if(!is_null($this->getUser())) {
            $userRoles = $this->getUser()->getRoles();
        } else {
            $userRoles = [];
        }

        if (in_array("ROLE_ADMIN", $userRoles)) {
            $responses = $this->getDoctrine()->getRepository(Response::class)->findBy(
                ['question' => $question],
                ['createdAt' => 'ASC']
            );
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
    
    /**
     * @Route("/question/search/", name="search_response")
     */
    public function searchResponse(Request $request, PaginatorInterface $paginator)
    {   
        $search = $request->query->get('search-input');

        if(!is_null($this->getUser())) {
            $userRoles = $this->getUser()->getRoles();
        } else {
            $userRoles = [];
        }

        if (in_array("ROLE_ADMIN", $userRoles)) {
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
