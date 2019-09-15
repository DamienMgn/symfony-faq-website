<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Question;
use App\Entity\Response;
use App\Form\RegisterUserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="account")
     */
    public function index()
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $userId = $this->getUser()->getId();
        $userInformations = $this->getDoctrine()->getRepository(User::class)->find($userId);

        return $this->render('account/index.html.twig', [
            'user' => $userInformations,
        ]);
    }

    /**
     * @Route("/account/update", name="account_update")
     */
    public function addQuestion(Request $request, UserPasswordEncoderInterface $encoder)
    {   

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $form = $this->createForm(RegisterUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $plainPassword = $user->getPassword();
            $encoded = $encoder->encodePassword($user, $plainPassword);
            $user->setPassword($encoded);
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
    
            return $this->redirectToRoute('account');
        }

        return $this->render('account/update.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/questions", name="account_questions")
     */
    public function userQuestions()
    {   
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        
        $questions = $this->getDoctrine()->getRepository(Question::class)->findBy(['user' => $user]);

        return $this->render('account/user_questions.html.twig', [
            'questions' => $questions,
        ]);

    }

    /**
     * @Route("/account/responses", name="account_responses")
     */
    public function userResponses()
    {   
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        
        $responses = $this->getDoctrine()->getRepository(Response::class)->findBy(['user' => $user]);

        return $this->render('account/user_responses.html.twig', [
            'responses' => $responses,
        ]);

    }
}
