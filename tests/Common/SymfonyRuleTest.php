<?php

declare(strict_types=1);

namespace Vjik\Yii\ValidatorSymfonyRule\Tests\Common;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\All as SymfonyAll;
use Symfony\Component\Validator\Constraints\Collection as SymfonyCollection;
use Symfony\Component\Validator\Constraints\GreaterThan as SymfonyGreaterThan;
use Symfony\Component\Validator\Constraints\Length as SymfonyLength;
use Symfony\Component\Validator\Constraints\NotBlank as SymfonyNotBlank;
use Vjik\Yii\ValidatorSymfonyRule\SymfonyRule;
use Vjik\Yii\ValidatorSymfonyRule\SymfonyRuleHandler;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;

final class SymfonyRuleTest extends TestCase
{
    public function testDefaults(): void
    {
        $rule = new SymfonyRule([]);

        $this->assertSame('symfony-rule', $rule->getName());
        $this->assertSame([], $rule->getConstraints());
        $this->assertNull($rule->getSkipOnEmpty());
        $this->assertFalse($rule->shouldSkipOnError());
        $this->assertNull($rule->getWhen());
    }

    public function dataOptions(): array
    {
        return [
            [
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
                new SymfonyRule([]),
            ],
            [
                [
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
                new SymfonyRule([], skipOnEmpty: true),
            ],
            [
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
                new SymfonyRule([], skipOnError: true),
            ],
        ];
    }

    /**
     * @dataProvider dataOptions
     */
    public function testOptions(array $expected, SymfonyRule $rule): void
    {
        $this->assertSame($expected, $rule->getOptions());
    }

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

    public function dataValuePath(): array
    {
        return [
            'associative-array' => [
                [
                    'name.first-name' => ['This value is too short. It should have 10 characters or more.'],
                    'name.last-name' => ['This value is too short. It should have 7 characters or more.'],
                ],
                [
                    'name' => [
                        'first-name' => 'Barbara',
                        'last-name' => 'Liskov',
                    ],
                ],
                new SymfonyCollection([
                    'name' => new SymfonyCollection([
                        'first-name' => new SymfonyLength(min: 10),
                        'last-name' => new SymfonyLength(min: 7),
                    ]),
                ]),
            ],
            'list' => [
                [
                    '1' => ['This value is too short. It should have 3 characters or more.'],
                    '2' => ['This value is too short. It should have 3 characters or more.'],
                    '4' => ['This value is too short. It should have 3 characters or more.'],
                ],
                [
                    'kate',
                    'bo',
                    'a',
                    'barbara',
                    'x'
                ],
                new SymfonyAll(
                    new SymfonyLength(min: 3),
                ),
            ],
            'object' => [
                [
                    'name' => ['This value is too short. It should have 5 characters or more.'],
                    'numbers.0' => ['This value should be greater than 2.'],
                    'numbers.2' => ['This value should be greater than 2.'],
                ],
                new class() {
                    private string $name = 'mike';
                    private array $numbers = [1, 3, 2, 5];
                },
                new SymfonyCollection([
                    'name' => new SymfonyLength(min: 5),
                    'numbers' => new SymfonyAll(new SymfonyGreaterThan(2))
                ]),
            ],
        ];
    }

    /**
     * @dataProvider dataValuePath
     */
    public function testValuePath(array $expectedMessages, mixed $data, Constraint $constraint): void
    {
        $result = (new Validator())->validate($data, new SymfonyRule($constraint));

        $this->assertSame($expectedMessages, $result->getErrorMessagesIndexedByPath());
    }

    public function testInvalidRule(): void
    {
        $handler = new SymfonyRuleHandler();
        $rule = new SymfonyLength(min: 5);
        $context = new ValidationContext();

        $this->expectException(UnexpectedRuleException::class);
        $this->expectExceptionMessage(
            'Expected "Vjik\Yii\ValidatorSymfonyRule\SymfonyRule", but "Symfony\Component\Validator\Constraints\Length" given.'
        );
        $handler->validate(7, $rule, $context);
    }
}
