<?php

declare(strict_types=1);

namespace App\Omdb\Client;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

final class NoResultException extends Exception implements HttpExceptionInterface
{
    private function __construct(
        string     $message = "",
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $this->getStatusCode(), $previous);
    }

    public static function forId(string $imdbId, ?Throwable $previous = null): self
    {
        return new self("No movie found on OMDB API for IMDB ID '{$imdbId}'.", $previous);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getHeaders(): array
    {
        return [];
    }
}
