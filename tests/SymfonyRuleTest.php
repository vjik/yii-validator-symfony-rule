<?php

declare(strict_types=1);

namespace Vjik\Yii\ValidatorSymfonyRule\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Length as SymfonyLength;
use Vjik\Yii\ValidatorSymfonyRule\SymfonyRule;
use Yiisoft\Validator\Validator;

final class SymfonyRuleTest extends TestCase
{
    public function testBase(): void
    {
        $result = (new Validator())->validate(
            'Yii',
            new SymfonyRule(new SymfonyLength(['min' => 7]))
        );

        $this->assertSame(
            [
                '' => ['This value is too short. It should have 7 characters or more.'],
            ],
            $result->getErrorMessagesIndexedByPath()
        );
    }
}
