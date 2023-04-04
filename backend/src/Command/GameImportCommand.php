<?php

namespace App\Command;

use App\Entity\Game;
use App\Entity\Publisher;
use App\Repository\GameRepository;
use App\Repository\PublisherRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'game:import',
    description: 'Add a short description for your command',
)]
class GameImportCommand extends Command
{
    /** @var Publisher[] */
    private array $publisherCache = [];


    public function __construct(private readonly PublisherRepository $publisherRepository, private readonly GameRepository $gameRepository, private readonly UserRepository $userRepository)
    {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = $this->userRepository->findOneBy(['username' => 'harryPotter']);
        $io = new SymfonyStyle($input, $output);
        $head = [
            0 => 'Nr.',
            1 => '  ',
            2 => 'Titel',
            3 => 'Art',
            4 => 'Verlag',
            5 => 'min.',
            6 => 'max.',
            7 => 'min. Dauer',
            8 => 'EAN',
            9 => 'Anmerkung',
            10 => 'ausgeliehen an',
            11 => ' Wert',
        ];
        $row = 1;
        $games = [];
        if (($handle = fopen("games.csv", "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $io->write('.');
                if ($row % 50 === 0) {
                    $io->writeln('');
                }
                if ($row === 1) {
                    $row++;
                    continue;
                }
                $row++;
                $games[] = ['title' => $data[2], 'code' => $data[8]];
                //$this->addGame($data, $user, $io);
                if ($row === 20 ) break;
            }
            fclose($handle);
            $io->writeln('');
            $io->writeln($row . ' lines handled');
        }
$io->writeln(var_export($games, true));
        return Command::SUCCESS;
    }

    /**
     * @param bool|array            $data
     * @param \App\Entity\User|null $user
     * @param SymfonyStyle          $io
     */
    protected function addGame(bool|array $data, ?\App\Entity\User $user, SymfonyStyle $io): void
    {
        try {


            if (strtolower($data[1]) === 'x') {
                $game = $this->gameRepository->findOneBy(['inventoryNumber' => (int)$data[0]]);
                if ($game === null) {
                    $game = new Game();
                    $game->setInventoryNumber((int)$data[0]);
                }
                $game->setCode(substr($data[8], 0, 19));
                $game->setName($data[2]);
                $game->setOwner($user);
                $publishers = $this->findPublisher($data[4]);
                foreach ($publishers as $publisher) {
                    $game->addPublisher($publisher);
                }
                if (is_numeric($data[5])) {
                    $game->setPlayerMin((int)$data[5]);
                }
                if (is_numeric($data[6])) {
                    $game->setPlayerMax((int)$data[6]);
                }
                if (is_numeric($data[7])) {
                    $game->setDurationMinuntesMin((int)$data[7]);
                }
                $game->setValue((float)(str_replace(',', '.', trim($data[11]))));
                $this->gameRepository->save($game, true);
            }
        } catch (\Throwable $t) {
            $io->error([$t->getMessage(), $data[0], $data[2]]);
            if ($t->getMessage() === 'The EntityManager is closed.') {
                die('??');
            }
        }
    }

    private function findPublisher(string $pub): array
    {
        $list = explode('/', $pub);
        $result = [];
        foreach ($list as $item) {
            $item = trim($item);
            $key = strtolower($item);
            if (!array_key_exists($key, $this->publisherCache)) {
                $publisher = $this->publisherRepository->findOneBy(['name' => $item]);
                if ($publisher === null) {
                    $publisher = new Publisher();
                    $publisher->setName($item);
                    $this->publisherRepository->save($publisher, true);
                }
                $this->publisherCache[$key] = $publisher;
            }
            $result[] = $this->publisherCache[$key];
        }

        return $result;
    }
}
