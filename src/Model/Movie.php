<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Genre as GenreEntity;
use App\Entity\Movie as MovieEntity;
use App\Omdb\Client\OmdbApiConsumerInterface;
use DateTimeImmutable;
use Symfony\Component\String\Slugger\SluggerInterface;
use function array_map;
use function explode;
use function str_starts_with;

/**
 * @phpstan-import-type OmdbMovieResult from OmdbApiConsumerInterface
 */
final class Movie
{
    public const SLUG_REGEX = '[a-zA-Z0-9-_]{3,}';

    /**
     * @param list<string> $genres
     */
    public function __construct(
        public readonly string            $slug,
        public readonly string            $title,
        public readonly string            $plot,
        public readonly DateTimeImmutable $releasedAt,
        public readonly string            $poster,
        public readonly array             $genres,
    )
    {
    }

    public static function fromEntity(MovieEntity $movieEntity): self
    {
        return new self(
            slug: $movieEntity->getSlug(),
            title: $movieEntity->getTitle(),
            plot: $movieEntity->getPlot(),
            releasedAt: $movieEntity->getReleasedAt(),
            poster: $movieEntity->getPoster(),
            genres: array_map(
                fn(GenreEntity $genreEntity): string => $genreEntity->getName(),
                $movieEntity->getGenres()->toArray(),
            )
        );
    }

    /**
     * @param OmdbMovieResult $movieOmdb
     */
    public static function fromOmdbResult(array $movieOmdb, SluggerInterface $slugger): self
    {
        return new self(
            slug: $slugger->slug($movieOmdb['Title'])->toString(),
            title: $movieOmdb['Title'],
            plot: $movieOmdb['Plot'],
            releasedAt: new DateTimeImmutable($movieOmdb['Released']),
            poster: $movieOmdb['Poster'],
            genres: explode(', ', $movieOmdb['Genre'])
        );
    }

    /**
     * @param list<MovieEntity> $movies
     *
     * @return list<Movie>
     */
    public static function fromEntities(array $movies): array
    {
        return array_map(self::fromEntity(...), $movies);
    }

    public function isRemotePoster(): bool
    {
        return str_starts_with($this->poster, 'http');
    }
}
