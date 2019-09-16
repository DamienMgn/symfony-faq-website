<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    private static $tags = [
        'cinéma',
        'musique',
        'science',
        'chimie',
        'physique',
        'pop',
        'culture',
        'folk',
        'nature',
        'série',
        'jardin',
        'food',
        'livre',
        'rap',
        'technologie',
        'univers',
        'art',
        'peinture',
        'sculture',
        'bd',
        'comics',
        'vidéo',
        'sport',
        'politique',
        'foot',
        'vélo'
    ];

    public function load(ObjectManager $manager)
    {

        foreach(self::$tags as $tag) {
            $tagEntity = new Tag();
            $tagEntity->setName($tag);

            $manager->persist($tagEntity);
        }
        
        $manager->flush();
    }
}
