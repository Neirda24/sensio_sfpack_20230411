<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Movie;
use Symfony\Contracts\EventDispatcher\Event;

final class MovieAddedEvent extends Event
{
    public function __construct(
        public readonly Movie $movie,
    )
    {
    }
}
