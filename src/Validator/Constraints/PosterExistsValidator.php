<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function file_exists;
use function rtrim;

final class PosterExistsValidator extends ConstraintValidator
{
    private readonly string $posterAssetsPath;

    public function __construct(
        string $posterAssetsPath
    )
    {
        $this->posterAssetsPath = rtrim($posterAssetsPath, '/');
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PosterExists) {
            throw new UnexpectedTypeException($constraint, PosterExists::class);
        }

        if (null === $value) {
            return;
        }

        $fullPath = "{$this->posterAssetsPath}/{$value}";
        if (!file_exists($fullPath)) {
            $violationBuilder = $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ filename }}', $value);

            $violationBuilder->addViolation();
        }
    }
}
