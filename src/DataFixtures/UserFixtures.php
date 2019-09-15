<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new User();

        $admin->setEmail('admin@a.fr');
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setUserName('admin');

        $encoded = $this->encoder->encodePassword($admin, 'admin');
        $admin->setPassword($encoded);

        $manager->persist($admin);

        $user = new User();

        $user->setEmail('user@u.fr');
        $user->setRoles(["ROLE_USER"]);
        $user->setUserName('user');

        $encoded = $this->encoder->encodePassword($user, 'user');
        $user->setPassword($encoded);

        $manager->persist($user);

        $manager->flush();
    }
}
