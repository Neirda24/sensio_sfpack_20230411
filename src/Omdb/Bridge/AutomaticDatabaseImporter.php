<?php

declare(strict_types=1);

namespace App\Omdb\Bridge;

use App\Omdb\Client\OmdbApiConsumerInterface;

final class AutomaticDatabaseImporter implements OmdbApiConsumerInterface
{
    public function __construct(
        private readonly OmdbApiConsumerInterface        $omdbApiConsumer,
        private readonly OmdbToDatabaseImporterInterface $omdbToDatabaseImporter,
    )
    {
    }

    public function getById(string $imdbId): array
    {
        $toImport = $this->omdbApiConsumer->getById($imdbId);

        $this->omdbToDatabaseImporter->importFromApiData($toImport, true);

        return $toImport;
    }

    public function searchByTitle(string $title): array
    {
        return $this->omdbApiConsumer->searchByTitle($title);
    }
}
