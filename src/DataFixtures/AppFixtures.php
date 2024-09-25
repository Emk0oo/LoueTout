<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $superAdmin = new User();
        $superAdmin->setEmail('admin@test.com')
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setAddress('1 rue du test')
            ->setPassword('password')
            ->setFirstName('Super')
            ->setLastName('Admin')
            ->setPhone('1234567890')
            ->setInstance(null);
        $manager->persist($superAdmin);
        $manager->flush();
    }
}
