<?php

declare(strict_types=1);

namespace Vjik\Yii\ValidatorSymfonyRule;

use Attribute;
use Closure;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Symfony validation constraints wrapper for usage with Yii Validator.
 *
 * Usage example:
 *
 * ```php
 * use Symfony\Component\Validator\Constraints\Email;
 * use Symfony\Component\Validator\Constraints\NotBlank;
 * use Vjik\Yii\ValidatorSymfonyRule\SymfonyRule;
 *
 * final class PropertyAttribute
 * {
 *   #[SymfonyRule(new NotBlank())]
 *   public string $name = '';
 *
 *   #[SymfonyRule([
 *     new NotBlank(),
 *     new Email(),
 *   ])]
 *   public string $email = '';
 * }
 * ```
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class SymfonyRule implements
    RuleWithOptionsInterface,
    SkipOnEmptyInterface,
    SkipOnErrorInterface,
    WhenInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @var Constraint[]
     * @psalm-var list<Constraint>
     */
    private array $constraints;

    /**
     * @var bool|callable|null
     */
    private $skipOnEmpty;

    /**
     * @param Constraint|Constraint[] $constraint Single or array of Symfony validation constraints.
     * @param bool|callable|null $skipOnEmpty Whether skip rule on empty value or not, and which value consider as
     * empty. More details in {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError A boolean value where `true` means to skip rule when the previous one errored
     * and `false` - do not skip.
     * @param Closure|null $when The closure that allow to apply rule under certain conditions only. More details
     * in {@see SkipOnErrorInterface}.
     *
     * @psalm-param WhenType $when
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        Constraint|array $constraint,
        bool|callable|null $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
        $this->setConstraints($constraint);
        $this->skipOnEmpty = $skipOnEmpty;
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

    public function getOptions(): array
    {
        return [
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }
}
