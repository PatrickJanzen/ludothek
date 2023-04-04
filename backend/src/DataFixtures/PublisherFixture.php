<?php

namespace App\DataFixtures;

use App\Entity\Game;
use App\Entity\Publisher;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PublisherFixture extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $pub = new Publisher();
            $pub->setName($faker->company());

            $manager->persist($pub);
            $this->setReference('pub_' . $i, $pub);
        }

        $manager->flush();
    }
}
