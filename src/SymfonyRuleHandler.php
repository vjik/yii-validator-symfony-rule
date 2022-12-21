<?php

declare(strict_types=1);

namespace Vjik\Yii\ValidatorSymfonyRule;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class SymfonyRuleHandler implements RuleHandlerInterface
{
    private SymfonyValidatorInterface $symfonyValidator;

    public function __construct(?SymfonyValidatorInterface $symfonyValidator = null)
    {
        $this->symfonyValidator = $symfonyValidator ?? Validation::createValidator();
    }

    /**
     * @throws UnexpectedRuleException
     */
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof SymfonyRule) {
            throw new UnexpectedRuleException(SymfonyRule::class, $rule);
        }

        $violations = $this->symfonyValidator->validate($value, $rule->getConstraints());

        $result = new Result();
        foreach ($violations as $violation) {
            $result->addError(
                message: (string) $violation->getMessage(),
                valuePath: $this->prepareValuePath($violation->getPropertyPath()),
            );
        }

        return $result;
    }

    /**
     * @psalm-return list<string>
     */
    private function prepareValuePath(string $propertyPath): array
    {
        if ($propertyPath === '') {
            return [];
        }

        $propertyPath = trim($propertyPath, '[');
        $propertyPath = strtr($propertyPath, [']' => '', '[' => '.']);
        return explode('.', $propertyPath);
    }
}
