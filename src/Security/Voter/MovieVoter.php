<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Model\Movie;
use Psr\Clock\ClockInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class MovieVoter extends Voter
{
    public const VIEW_DETAILS = 'MOVIE_VIEW_DETAILS';

    public function __construct(
        private readonly ClockInterface $clock,
        private readonly Security $security,
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::VIEW_DETAILS === $attribute
            && $subject instanceof Movie;
    }

    /**
     * @param Movie $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $minAgeRequired = $subject->rated->minAgeRequired();

        if (0 === $minAgeRequired) {
            return true;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN') === true) {
            return true;
        }

        return $user->isOlderThanOrEqual($minAgeRequired, $this->clock->now());
    }
}
