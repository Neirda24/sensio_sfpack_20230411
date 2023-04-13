<?php

declare(strict_types=1);

namespace App\Omdb\Client;

/**
 * @phpstan-type OmdbMovieResult array{Title: string, Year: string, Rated: string, Released: string, Genre: string, Plot: string, Poster: string, imdbID: string, Type: string, Response: string}
 * @phpstan-type OmdbMovieSearchResults list<array{Title: string, Year: string, imdbID: string, Type: string, Poster: string}>
 */
interface OmdbApiConsumerInterface
{
    /**
     * @return OmdbMovieResult
     */
    public function getById(string $imdbId): array;

    /**
     * @return OmdbMovieSearchResults
     */
    public function searchByTitle(string $title): array;
}
