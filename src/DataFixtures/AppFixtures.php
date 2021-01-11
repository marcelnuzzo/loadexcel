<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Student;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setFirstname("Marcel")
            ->setLastname("Nuzzo")
            ->setEmail("nuzzo.marcel@aliceadsl.fr")
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->passwordEncoder->encodePassword(
                $user,
                '1234'
            ));
        
        $manager->persist($user);
        
        for($i=1; $i<10; $i++) {
            $student = new Student();
            $student->setName("name ". $i)
                    ->setNoteMath(mt_rand(1, 20))
                    ->setNoteFrancais(mt_rand(1, 20))
                    ;
            $manager->persist($student);
        }
        

        $manager->flush();
    }
}
