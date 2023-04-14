<?php

namespace App\Command;

use App\Entity\Movie as MovieEntity;
use App\Omdb\Bridge\OmdbToDatabaseImporterInterface;
use App\Omdb\Client\NoResultException;
use App\Omdb\Client\OmdbApiConsumerInterface;
use App\Repository\MovieRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use function array_key_first;
use function array_reduce;
use function count;
use function sprintf;

#[AsCommand(
    name: 'app:omdb:movies:import',
    description: 'Import one or more movies from the OMDB API into your database.',
    aliases: [
        'omdb:movies:import'
    ]
)]
class OmdbMoviesImportCommand extends Command
{
    public function __construct(
        private readonly OmdbApiConsumerInterface        $omdbApiConsumer,
        private readonly OmdbToDatabaseImporterInterface $omdbToDatabaseImporter,
        private readonly MovieRepository                 $movieRepository,
    )
    {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id-or-title', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Can either be a valid IMDB ID or a Title to search for.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Won\'t import movies to database. Only display summary of actions.')
            ->setHelp(<<<EOT
            The <info>%command.name%</info> import movies data from OMDB api to database :
            
            Using only titles
                <info>php %command.full_name% "movie1-title" "movie2-title" ...</info>
                
            Using only IMDB ID's
                <info>php %command.full_name% "id1" "id2" ...</info>
                
            Or mixing both
                <info>php %command.full_name% "id1" "movie2-title" ...</info>
            EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('OMDB Import');

        /** @var list<string> $idOrTitleList */
        $idOrTitleList = $input->getArgument('id-or-title');
        $io->note(sprintf('Trying to import %d movies', count($idOrTitleList)));

        $moviesImported = [];
        $moviesFailed = [];

        foreach ($idOrTitleList as $idOrTitle) {
            $movie = $this->import($io, $idOrTitle);

            if (null === $movie) {
                $moviesFailed[] = $idOrTitle;
                continue;
            }

            $moviesImported[] = [$idOrTitle, $movie];
        }

        $isDryRun = $input->getOption('dry-run');
        if (false === $isDryRun) {
            $this->movieRepository->flush();
        }

        if (count($moviesImported) > 0) {
            $verb = false === $isDryRun ? 'were' : 'would be';
            $io->success("These movies {$verb} imported :");
            $io->table(
                ['ID', 'Search Query', 'Title'],
                array_reduce($moviesImported, static function (array $rows, array $movieImported): array {
                    /** @var MovieEntity $movie */
                    [$idOrTitle, $movie] = $movieImported;

                    $rows[] = [$movie->getId(), $idOrTitle, "{$movie->getTitle()} ({$movie->getYear()})"];

                    return $rows;
                }, [])
            );
        }

        if (count($moviesFailed) > 0) {
            $io->error('Those search terms could not be found or were skipped :');
            $io->listing($moviesFailed);
        }

        return Command::SUCCESS;
    }

    private function import(SymfonyStyle $io, string $idOrTitle): ?MovieEntity
    {
        $io->section("'{$idOrTitle}'");

        return $this->tryImportAsImdbId($io, $idOrTitle) ?? $this->searchAndImportByTitle($io, $idOrTitle);
    }

    private function tryImportAsImdbId(SymfonyStyle $io, string $imdbId): ?MovieEntity
    {
        try {
            $result = $this->omdbApiConsumer->getById($imdbId);
        } catch (NoResultException) {
            return null;
        }

        $acceptImport = $io->askQuestion(new ConfirmationQuestion("Do you wish to import {$result['Title']} ({$result['Year']}) ?", true));

        if (false === $acceptImport) {
            $io->warning('   >>> Skipping');
            return null;
        }

        return $this->omdbToDatabaseImporter->importFromApiData($result, false);
    }

    private function searchAndImportByTitle(SymfonyStyle $io, string $title): ?MovieEntity
    {
        try {
            $searchResults = $this->omdbApiConsumer->searchByTitle($title);
        } catch (NoResultException) {
            return null;
        }

        if (count($searchResults) === 0) {
            return null;
        }

        /** @var array<string, string> $choices */
        $choices = array_reduce($searchResults, static function (array $choices, array $searchResult): array {
            $choices[$searchResult['imdbID']] = "{$searchResult['Title']} ({$searchResult['Year']})";

            return $choices;
        }, []);

        if (count($choices) === 1) {
            $selectedChoice = array_key_first($choices);
            $io->info("'{$selectedChoice}' is the only result. Selecting.");
        } else {
            $choices['none'] = 'None of the above.';
            $selectedChoice = $io->choice('Which movie would you like to import ?', $choices);

            if ('none' === $selectedChoice) {
                $io->warning('   >>> Skipping');
                return null;
            }
        }

        return $this->tryImportAsImdbId($io, $selectedChoice);
    }
}
