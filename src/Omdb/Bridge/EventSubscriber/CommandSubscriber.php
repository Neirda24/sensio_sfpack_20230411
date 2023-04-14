<?php

declare(strict_types=1);

namespace App\Omdb\Bridge\EventSubscriber;

use App\Omdb\Bridge\AutomaticDatabaseImporterConfig;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CommandSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly AutomaticDatabaseImporterConfig $automaticDatabaseImporterConfig
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleCommandEvent::class => [
                ['skipAutoImport', 0]
            ],
            ConsoleTerminateEvent::class => [
                ['restoreAutoImport', 0]
            ],
        ];
    }

    public function skipAutoImport(ConsoleCommandEvent $event): void
    {
        $this->automaticDatabaseImporterConfig->skip();
    }

    public function restoreAutoImport(ConsoleTerminateEvent $event): void
    {
        $this->automaticDatabaseImporterConfig->restore();
    }
}
