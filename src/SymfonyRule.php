<?php

declare(strict_types=1);

namespace Vjik\Yii\ValidatorSymfonyRule;

use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;

final class SymfonyRule implements RuleInterface
{
    /**
     * @var Constraint[]
     * @psalm-var list<Constraint>
     */
    private array $constraints;

    /**
     * @param Constraint|Constraint[] $constraint
     */
    public function __construct(Constraint|array $constraint)
    {
        $this->setConstraints($constraint);
    }

    /**
     * @return Constraint[]
     * @psalm-return list<Constraint>
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function getName(): string
    {
        return 'symfony-rule';
    }

    public function getHandler(): string|RuleHandlerInterface
    {
        return SymfonyRuleHandler::class;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function setConstraints(Constraint|array $constraints): void
    {
        if ($constraints instanceof Constraint) {
            $this->constraints = [$constraints];
            return;
        }

        $this->constraints = [];
        foreach ($constraints as $constraint) {
            if (!$constraint instanceof Constraint) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Symfony constraint must be object that extends "%s". Got "%s".',
                        Constraint::class,
                        get_debug_type($constraint)
                    ),
                );
            }
            $this->constraints[] = $constraint;
        }
    }
}
