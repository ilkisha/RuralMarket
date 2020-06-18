<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
     private $passwordEncoder;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('i.shalvarkov@gmail.com');
        $user->setFullName('Iliyan Shalvarkov');
        $password = '123';
        $passwordHash = $this->passwordEncoder->encodePassword($user, $password);
        $user->setPassword($passwordHash);

        $manager->persist($user);
        $manager->flush();
    }
}
