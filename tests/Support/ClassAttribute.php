<?php

declare(strict_types=1);

namespace Vjik\Yii\ValidatorSymfonyRule\Tests\Support;

use Symfony\Component\Validator\Constraints\Collection as SymfonyCollection;
use Symfony\Component\Validator\Constraints\GreaterThan as SymfonyGreaterThan;
use Vjik\Yii\ValidatorSymfonyRule\SymfonyRule;

#[SymfonyRule(
    new SymfonyCollection([
        'a' => new SymfonyGreaterThan(7),
    ]),
)]
final class ClassAttribute
{
    private int $a = 3;
}
