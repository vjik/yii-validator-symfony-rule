<?php

declare(strict_types=1);

namespace Vjik\Yii\ValidatorSymfonyRule\Tests\Support;

use Symfony\Component\Validator\Constraints\CssColor;
use Symfony\Component\Validator\Constraints\NotEqualTo;
use Symfony\Component\Validator\Constraints\Positive;
use Vjik\Yii\ValidatorSymfonyRule\SymfonyRule;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;

final class Car
{
    #[Required]
    #[HasLength(min: 3, skipOnEmpty: true)]
    public string $name = '';

    #[Required]
    #[SymfonyRule(
        new CssColor(CssColor::RGB),
        skipOnEmpty: true,
    )]
    public string $cssColor = '#1123';

    #[SymfonyRule([
        new Positive(),
        new NotEqualTo(13),
    ])]
    public int $number = 13;
}
