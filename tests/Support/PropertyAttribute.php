<?php

declare(strict_types=1);

namespace Vjik\Yii\ValidatorSymfonyRule\Tests\Support;

use Symfony\Component\Validator\Constraints\Length as SymfonyLength;
use Symfony\Component\Validator\Constraints\NotBlank as SymfonyNotBlank;
use Vjik\Yii\ValidatorSymfonyRule\SymfonyRule;

final class PropertyAttribute
{
    #[SymfonyRule(new SymfonyNotBlank())]
    #[SymfonyRule(new SymfonyLength(min: 3))]
    private string $name = '';
}
