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
        $tags = $this->getDoctrine()->getRepository(Tag::class)->findAll();

        $currentTag = $this->getDoctrine()->getRepository(Tag::class)->find($tag);

        return $this->render('tag/index.html.twig', [
            'tags' => $tags,
            'currentTag' => $currentTag
        ]);
    }
}
