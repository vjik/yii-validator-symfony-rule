<?php

declare(strict_types=1);

namespace Vjik\Yii\ValidatorSymfonyRule\Tests\Php81;

use PHPUnit\Framework\TestCase;
use Vjik\Yii\ValidatorSymfonyRule\Tests\Support\Car;
use Vjik\Yii\ValidatorSymfonyRule\Tests\Support\ClassAttribute;
use Vjik\Yii\ValidatorSymfonyRule\Tests\Support\PropertyAttribute;
use Yiisoft\Validator\Validator;

final class SymfonyRuleTest extends TestCase
{
    public function testCar(): void
    {
        $result = (new Validator())->validate(new Car());

        $this->assertSame(
            [
                'name' => ['Value cannot be blank.'],
                'cssColor' => ['This value is not a valid CSS color.'],
                'number' => ['This value should not be equal to 13.'],
            ],
            $result->getErrorMessagesIndexedByPath(),
        );
    }

    public function testPropertyAttribute(): void
    {
        $result = (new Validator())->validate(new PropertyAttribute());

        $this->assertSame(
            [
                'name' => [
                    'This value should not be blank.',
                    'This value is too short. It should have 3 characters or more.',
                ],
            ],
            $result->getErrorMessagesIndexedByPath(),
        );
    }

    public function testClassAttribute(): void
    {
        $result = (new Validator())->validate(new ClassAttribute());

        $this->assertSame(
            [
                '' => ['This collection should contain 7 elements or more.'],
            ],
            $result->getErrorMessagesIndexedByPath(),
        );
    }
}
