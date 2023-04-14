<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function array_filter;
use function array_map;
use function implode;
use function sprintf;

final class MovieSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly Security $security,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MovieAddedEvent::class => [
                ['notifyOtherAdminsAboutNewMovie', 0]
            ],
        ];
    }

    public function notifyOtherAdminsAboutNewMovie(MovieAddedEvent $movieAddedEvent): void
    {
        $allAdmins = $this->userRepository->listAllAdmins();

        if ($this->security->isGranted('ROLE_ADMIN') === true) {
            /** @var User $currentUser */
            $currentUser = $this->security->getUser();

            $allAdmins = array_filter($allAdmins, static function (User $user) use ($currentUser): bool {
                return $currentUser->getUserIdentifier() !== $user->getUserIdentifier();
            });
        }

        dump(sprintf(
            'Would notify about "%s" movie being added to : "%s"',
            $movieAddedEvent->movie->getTitle(),
            implode(', ', array_map(static function (User $user): string {
                return $user->getUsername();
            }, $allAdmins))
        ));
    }
}
