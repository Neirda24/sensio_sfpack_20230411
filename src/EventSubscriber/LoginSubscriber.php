<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\UserRepository;
use Psr\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ClockInterface $clock,
        private readonly UserRepository $userRepository,
    )
    {
    }

    public function updateLastLoggedIn(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $user->setLastLoggedIn($this->clock->now());
        $this->userRepository->save($user, true);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => [
                ['updateLastLoggedIn', 0]
            ],
        ];
    }
}
