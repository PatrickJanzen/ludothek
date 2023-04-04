<?php

namespace App\DataFixtures;

use App\Entity\User;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPERADMIN']);
        $user->setMemberSince(new CarbonImmutable());
        $user->setActive(true);
        $user->setName('Harry Potter');
        $user->setUsername('harrypotter');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'AlbusDumbledore'));
        $this->setReference('masterUser', $user);
        $manager->persist($user);

        $this->fakeMeeples($manager);

        $manager->flush();
    }

    private function fakeMeeples(ObjectManager $manager)
    {
        $faker = Factory::create();
        $date = new CarbonImmutable();
        for ($i = 0; $i < 10; $i++) {
            $meeple = new User();
            $meeple->setRoles(['ROLE_USER']);
            $meeple->setActive(true);
            $meeple->setName($faker->firstName() . ' ' . $faker->lastName());
            $meeple->setUsername($faker->userName());
            $meeple->setMemberSince($date);
            $meeple->setPassword($this->passwordHasher->hashPassword($meeple, 'password'));
            $manager->persist($meeple);
        }
    }
}
