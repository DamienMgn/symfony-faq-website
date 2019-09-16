<?php

namespace App\Controller;

use App\Entity\Tag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagController extends AbstractController
{
    /**
     * @Route("/tag/{id}", name="tag")
     */
    public function index(Tag $tag)
    {

        if(!is_null($this->getUser())) {
            $userRoles = $this->getUser()->getRoles();
        } else {
            $userRoles = [];
        }

        if (in_array("ROLE_ADMIN", $userRoles)) {
            $this->getDoctrine()->getRepository(Tag::class)->find($tag);
        } else {
            $this->getDoctrine()->getRepository(Tag::class)->findTagJoinQuestion('1', $tag);
        }

        $tags = $this->getDoctrine()->getRepository(Tag::class)->findAll();

        return $this->render('tag/index.html.twig', [
            'currentTag' => $tag,
            'allTags' => $tags
        ]);
    }
}
