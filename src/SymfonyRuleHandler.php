<?php

declare(strict_types=1);

namespace Vjik\Yii\ValidatorSymfonyRule;

use Symfony\Component\Validator\Validation;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class SymfonyRuleHandler implements RuleHandlerInterface
{
    /**
     * @throws UnexpectedRuleException
     */
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof SymfonyRule) {
            throw new UnexpectedRuleException(SymfonyRule::class, $rule);
        }

        $violations = Validation::createValidator()->validate($value, $rule->getConstraint());

        $result = new Result();
        foreach ($violations as $violation) {
            $result->addError(
                message: $violation->getMessage(),
                valuePath: [],
            );
        }

        return $result;
    }
}
