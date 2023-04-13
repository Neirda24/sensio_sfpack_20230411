<?php

declare(strict_types=1);

namespace App\Omdb\Bridge;

use App\Entity\Movie as MovieEntity;

interface OmdbToDatabaseImporterInterface
{
    public function importFromApiData(array $apiData, bool $flush = false): MovieEntity;
}
