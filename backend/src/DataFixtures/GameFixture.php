<?php

namespace App\DataFixtures;

use App\Entity\Game;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class GameFixture extends Fixture implements DependentFixtureInterface
{
    private array $data = [
        [
            'title' => 'Abalone',
            'code' => '5023117565021',
        ],

        [
            'title' => 'Das Große Pentago',
            'code' => '4002051690328',
        ],

        [
            'title' => 'Card \'n Go',
            'code' => 'keine EAN',
        ],

        [
            'title' => 'On Top',
            'code' => '4002051690342',
        ],

        [
            'title' => 'Der Hexer von Salem',
            'code' => '4002051690489',
        ],

        [
            'title' => 'Zicke Zacke Hühnerkacke',
            'code' => '4015682218007',
        ],

        [
            'title' => 'Schloss Schlotterstein',
            'code' => '4010168042190',
        ],

        [
            'title' => 'Der Goldene Kompass',
            'code' => '4002051690137',
        ],

        [
            'title' => 'Alcazar',
            'code' => '4002051690793',
        ],

        [
            'title' => 'Die Goldene Stadt',
            'code' => '4002051690205',
        ],

        [
            'title' => 'High Five!',
            'code' => '4002051691332',
        ],

        [
            'title' => 'Tintenblut',
            'code' => '4002051690656',
        ],

        [
            'title' => 'Geister, Geister, Schatzsuchmeister!',
            'code' => '0746775202293',
        ],

        [
            'title' => 'Kopf an Kopf',
            'code' => '4002051691158',
        ],

        [
            'title' => 'Abtei der Rätsel',
            'code' => '4002051692001',
        ],

        [
            'title' => 'Deutschland - Finden Sie Minden?',
            'code' => '4002051690243',
        ],

        [
            'title' => 'Targi',
            'code' => '4002051691479',
        ],

        [
            'title' => 'Das Geheimnis der Zauberer',
            'code' => '0887961101478',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $user = $this->getReference('masterUser');
        foreach ($this->data as $k => $g) {
            $game = new Game();
            $game->setName($g['title']);
            $game->setCode($g['code']);
            /* @var User $user */
            $game->setOwner($user);
            $game->setInventoryNumber($k);
            $game->setPlayerMin(1);
            $game->setPlayerMax(6);
            $game->setDurationMinuntesMin($k * 2);
            $game->setValue(12.99);
            $game->addPublisher($this->getReference('pub_'.random_int(1, 10)));

            $manager->persist($game);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixture::class, PublisherFixture::class];
    }
}
