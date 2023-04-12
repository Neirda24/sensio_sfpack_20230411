<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints\Length;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
final class Slug extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Length(min: 3, max: 255),
            new Choice(choices: ['new'], match: false),
        ];
    }
}
