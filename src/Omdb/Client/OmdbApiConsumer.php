<?php

declare(strict_types=1);

namespace App\Omdb\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;
use function array_key_exists;

/**
 * @phpstan-import-type OmdbMovieResult from OmdbApiConsumerInterface
 */
final class OmdbApiConsumer implements OmdbApiConsumerInterface
{
    public function __construct(
        private readonly HttpClientInterface $omdbApiClient,
    )
    {
    }

    public function getById(string $imdbId): array
    {
        $response = $this->omdbApiClient->request('GET', '/', [
            'query' => [
                'i' => $imdbId,
                'plot' => 'full',
                'r' => 'json'
            ],
        ]);

        try {
            /** @var OmdbMovieResult $result */
            $result = $response->toArray(true);
        } catch (Throwable $throwable) {
            throw NoResultException::forId($imdbId, $throwable);
        }

        if (array_key_exists('Response', $result) === true && 'False' === $result['Response']) {
            throw NoResultException::forId($imdbId);
        }

        return $result;
    }
}
