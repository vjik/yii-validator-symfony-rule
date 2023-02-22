<?php

declare(strict_types=1);

namespace Vjik\Yii\ValidatorSymfonyRule\Tests\Common;

use Countable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\All as SymfonyAll;
use Symfony\Component\Validator\Constraints\Collection as SymfonyCollection;
use Symfony\Component\Validator\Constraints\Count as SymfonyCount;
use Symfony\Component\Validator\Constraints\Length as SymfonyLength;
use Symfony\Component\Validator\Constraints\NotBlank as SymfonyNotBlank;
use Symfony\Component\Validator\Validation as SymfonyValidation;
use Symfony\Contracts\Translation\TranslatorInterface as SymfonyTranslatorInterface;
use Vjik\Yii\ValidatorSymfonyRule\SymfonyRule;
use Vjik\Yii\ValidatorSymfonyRule\SymfonyRuleHandler;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\RuleHandlerResolver\SimpleRuleHandlerContainer;
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
                new SymfonyRule(
                    new SymfonyCollection([
                        'name' => new SymfonyCollection([
                            'first-name' => new SymfonyLength(min: 10),
                            'last-name' => new SymfonyLength(min: 7),
                        ]),
                    ])
                ),
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
                new SymfonyRule(
                    new SymfonyAll(
                        new SymfonyLength(min: 3),
                    )
                ),
            ],
            'object' => [
                [
                    '' => ['This collection should contain 7 elements or more.'],
                ],
                new class() implements Countable {
                    private int $count = 3;

                    public function count(): int
                    {
                        return $this->count;
                    }
                },
                new SymfonyRule(
                    new SymfonyCount(min: 7),
                ),
            ],
            'empty-property-path' => [
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
                new Nested([
                    'name' => [
                        'first-name' => new SymfonyRule(new SymfonyLength(min: 10)),
                        'last-name' => new SymfonyRule(new SymfonyLength(min: 7)),
                    ],
                ]),
            ],
        ];
    }

    /**
     * @dataProvider dataValuePath
     */
    public function testValuePath(array $expectedMessages, mixed $data, mixed $rules): void
    {
        $result = (new Validator())->validate($data, $rules);

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

    public function testCustomSymfonyValidator(): void
    {
        $symfonyTranslator = new class() implements SymfonyTranslatorInterface {
            public function trans(
                string $id,
                array $parameters = [],
                string $domain = null,
                string $locale = null
            ): string {
                return 'hello';
            }

            public function getLocale(): string
            {
                return 'en';
            }
        };

        $symfonyValidator = SymfonyValidation::createValidatorBuilder()
            ->setTranslator($symfonyTranslator)
            ->getValidator();

        $validator = new Validator(new SimpleRuleHandlerContainer([
            SymfonyRuleHandler::class => new SymfonyRuleHandler($symfonyValidator),
        ]));

        $result = $validator->validate(
            'Yii',
            new SymfonyRule(new SymfonyLength(['min' => 7]))
        );

        $this->assertSame(
            [
                '' => ['hello'],
            ],
            $result->getErrorMessagesIndexedByPath()
        );
    }
}
