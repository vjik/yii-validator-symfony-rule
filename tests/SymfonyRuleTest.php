<?php

declare(strict_types=1);

namespace Vjik\Yii\ValidatorSymfonyRule\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Length as SymfonyLength;
use Symfony\Component\Validator\Constraints\NotBlank as SymfonyNotBlank;
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

    public function testConstraintsArray(): void
    {
        $result = (new Validator())->validate(
            '',
            new SymfonyRule([
                new SymfonyNotBlank(),
                new SymfonyLength(['min' => 7]),
            ]),
        );

        $this->assertSame(
            [
                '' => [
                    'This value should not be blank.',
                    'This value is too short. It should have 7 characters or more.',
                ],
            ],
            $result->getErrorMessagesIndexedByPath()
        );
    }

    public function testInvalidConstraintsArray(): void
    {
        $constraints = [
            new SymfonyNotBlank(),
            'length'
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Symfony constraint must be object that extends "Symfony\Component\Validator\Constraint". Got "string".'
        );
        new SymfonyRule($constraints);
    }
}
