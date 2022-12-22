<p align="center">
    <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    &nbsp; &nbsp; &nbsp;
    <img src="symfony-logo.svg" height="100px">
    <h1 align="center">Yii Validator Symfony Rule Adapter</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/vjik/yii-validator-symfony-rule/v/stable.png)](https://packagist.org/packages/vjik/yii-validator-symfony-rule)
[![Total Downloads](https://poser.pugx.org/vjik/yii-validator-symfony-rule/downloads.png)](https://packagist.org/packages/vjik/yii-validator-symfony-rule)
[![Build status](https://github.com/vjik/yii-validator-symfony-rule/workflows/build/badge.svg)](https://github.com/vjik/yii-validator-symfony-rule/actions?query=workflow%3Abuild)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fvjik%2Fyii-validator-symfony-rule%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/vjik/yii-validator-symfony-rule/master)
[![type-coverage](https://shepherd.dev/github/vjik/yii-validator-symfony-rule/coverage.svg)](https://shepherd.dev/github/vjik/yii-validator-symfony-rule)
[![static analysis](https://github.com/vjik/yii-validator-symfony-rule/workflows/static%20analysis/badge.svg)](https://github.com/vjik/yii-validator-symfony-rule/actions?query=workflow%3A%22static+analysis%22)
[![psalm-level](https://shepherd.dev/github/vjik/yii-validator-symfony-rule/level.svg)](https://shepherd.dev/github/vjik/yii-validator-symfony-rule)

The package provides validator rule `SymfonyRule` that allow use Symfony validation constraints in
[Yii Validator](https://github.com/yiisoft/validator).

## Requirements

- PHP 8.0 or higher.

## Installation

The package could be installed with [composer](https://getcomposer.org/download/):

```shell
composer require vjik/yii-validator-symfony-rule
```

## General usage

Wrap a symfony constraints to Yii rule `SymfonyRule` enough. For example:

```php
use Symfony\Component\Validator\Constraints\CssColor;
use Symfony\Component\Validator\Constraints\NotEqualTo;
use Symfony\Component\Validator\Constraints\Positive;
use Vjik\Yii\ValidatorSymfonyRule\SymfonyRule;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;

final class Car
{
    #[Required]
    #[HasLength(min: 3, skipOnEmpty: true)]
    public string $name = '';

    #[Required]
    #[SymfonyRule(
        new CssColor(CssColor::RGB), // Symfony constraint
        skipOnEmpty: true,
    )]
    public string $cssColor = '#1123';

    #[SymfonyRule([
        new Positive(), // Symfony constraint
        new NotEqualTo(13), // Symfony constraint
    ])]
    public int $number = 13;
}
```

## `SymfonyRule` rule parameters

**$constraint** — Single or array of Symfony validation constraints. Required.

**$skipOnEmpty** — Whether skip rule on empty value or not, and which value consider as empty. Defaults to `null`.

**$skipOnError** — A boolean value where `true` means to skip rule when the previous one errored and `false` — do not skip.
Defaults to `false`.

**$when** — The closure that allow to apply rule under certain conditions only. Defaults to `null`.

## `SymfonyRuleHandler` parameters

`$symfonyValidator` — Symfony validator instance. Defaults to validator created by 
`Symfony\Component\Validator\Validation::createValidator()`.

## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework with
[Infection Static Analysis Plugin](https://github.com/Roave/infection-static-analysis-plugin). To run it:

```shell
./vendor/bin/roave-infection-static-analysis-plugin
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

## License

The Yii Validator Symfony Rule Adapter is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.
