<?php

declare(strict_types=1);

namespace Vjik\Yii\ValidatorSymfonyRule;

use Symfony\Component\Validator\Constraint;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;

final class SymfonyRule implements RuleInterface
{
    public function __construct(
        private Constraint $constraint,
    ) {
    }

    public function getConstraint(): Constraint
    {
        return $this->constraint;
    }

    public function getName(): string
    {
        return 'symfony-rule';
    }

    public function getHandler(): string|RuleHandlerInterface
    {
        return SymfonyRuleHandler::class;
    }
}
