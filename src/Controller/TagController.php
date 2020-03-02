<?php

namespace App\Controller;

use App\Entity\Tag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagController extends AbstractController
{
    /**
     * @Route("/tag/{id}", name="tag")
     * 
     * Retourne les questions par tag
     */
    public function index(Tag $tag)
    {
        $tags = $this->getDoctrine()->getRepository(Tag::class)->findAll();

        return $this->render('tag/index.html.twig', [
            'currentTag' => $tag,
            'allTags' => $tags
        ]);
    }
}
