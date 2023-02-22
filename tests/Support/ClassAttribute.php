<?php

declare(strict_types=1);

namespace Vjik\Yii\ValidatorSymfonyRule\Tests\Support;

use Countable;
use Symfony\Component\Validator\Constraints\Count as SymfonyCount;
use Vjik\Yii\ValidatorSymfonyRule\SymfonyRule;

#[SymfonyRule(
    new SymfonyCount(min: 7),
)]
final class ClassAttribute implements Countable
{
    private int $count = 3;

    public function count(): int
    {
        return $this->count;
    }
}
