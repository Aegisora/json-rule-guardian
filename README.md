# Aegisora Json Rule Guardian

[![Latest Version](https://img.shields.io/packagist/v/aegisora/json-rule-guardian?style=flat-square)](https://packagist.org/packages/aegisora/json-rule-guardian)
[![Total Downloads](https://img.shields.io/packagist/dt/aegisora/json-rule-guardian?style=flat-square)](https://packagist.org/packages/aegisora/json-rule-guardian)
![Code Coverage Badge](./badge.svg)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
![PHPStan Badge](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)

Json Rule Guardian provides a simple shortcut for JSON string validation using `aegisora/guardian` and `aegisora/json-rule`.

It is designed for cases where you want to quickly check whether a value is a valid JSON string without manually creating validation pipelines.

This package is built on top of:

- [aegisora/guardian](https://github.com/Aegisora/guardian)
- [aegisora/json-rule](https://github.com/Aegisora/json-rule)

---

## ✨ Features
- 🔹 Simple shortcut API for `JsonRule`
- 🔹 Validates whether a value is a valid JSON string
- 🔹 Uses `aegisora/guardian` internally
- 🔹 Uses `aegisora/json-rule` internally
- 🔹 Supports custom validation exceptions
- 🔹 Fully compatible with the Aegisora ecosystem
- 🔹 Ready to use out of the box

---

## 📦 Installation

```shell
composer require aegisora/json-rule-guardian
```

---

## 🚀 Core Concept

This package wraps the common validation flow:

```php
$guardian->check($value, JsonRule::create(), new InvalidJsonException());
```

into a dedicated shortcut class:

```php
$jsonRuleGuardian->check($value, new InvalidJsonException());
```

Instead of manually creating `JsonRule` and passing it to `Guardian`, you can use `JsonRuleGuardian` directly.

---

## 🏗️ Basic Usage

```php
use Aegisora\Guardian\Exceptions\GuardianValidationException;
use Aegisora\Guardian\Guardian;
use Aegisora\RuleGuardians\JsonRule\JsonRuleGuardian;

$guardian = new Guardian();

$jsonRuleGuardian = new JsonRuleGuardian($guardian);

try {
    $jsonRuleGuardian->check('{"key":"value"}');
    // value is a valid JSON string
} catch (GuardianValidationException $exception) {
    // value is not a valid JSON string
}
```

---

## 🧩 Usage with Custom Exception

You may provide your own exception for validation failure.

```php
use Aegisora\Guardian\Guardian;
use Aegisora\RuleGuardians\JsonRule\JsonRuleGuardian;
use App\Exceptions\InvalidJsonException;

$guardian = new Guardian();

$jsonRuleGuardian = new JsonRuleGuardian($guardian);

$jsonRuleGuardian->check('{invalid json}', new InvalidJsonException());
```

If the value is not a valid JSON string, the provided exception will be thrown.

This is useful when validation errors should have domain-specific meaning.

---

## 🧪 Example in Application Service

```php
use Aegisora\RuleGuardians\JsonRule\JsonRuleGuardian;
use App\Exceptions\InvalidJsonException;

final class PayloadService
{
    private JsonRuleGuardian $jsonRuleGuardian;

    public function __construct(
        JsonRuleGuardian $jsonRuleGuardian
    ) {
        $this->jsonRuleGuardian = $jsonRuleGuardian;
    }

    /**
     * @param mixed $value
     */
    public function process($value): void
    {
        $this->jsonRuleGuardian->check($value, new InvalidJsonException());

        // business logic for valid JSON string
    }
}
```

---

## 🚨 Exceptions

This package does not define its own exception types. All errors are raised by the underlying `aegisora/guardian` package.

Both exceptions extend the abstract base class
`Aegisora\Guardian\Exceptions\GuardianException`,
so you can catch every validation error with a single `catch`:

```php
use Aegisora\Guardian\Exceptions\GuardianException;

try {
    $jsonRuleGuardian->check($value);
} catch (GuardianException $exception) {
    // handles GuardianValidationException and GuardianExecutingRuleException
}
```

### `GuardianValidationException`

Thrown when validation fails and no custom exception is provided.

```php
use Aegisora\Guardian\Exceptions\GuardianValidationException;

try {
    $jsonRuleGuardian->check('{invalid json}');
} catch (GuardianValidationException $exception) {
    echo $exception->getRuleCode(); // "json_rule"
}
```

### `GuardianExecutingRuleException`

Thrown when the underlying rule execution fails, for example when the value is not a string.

`Aegisora\Guardian\Exceptions\GuardianExecutingRuleException`

---

## 🧩 API

### `JsonRuleGuardian::check()`

```php
/**
 * @param mixed $value
 */
public function check(
    $value,
    ?\Throwable $exception = null
): void
```

Parameters:
- `$value` *(mixed)* — value to validate as a JSON string
- `$exception` *(?\Throwable, default `null`)* — optional custom exception thrown on validation failure

Returns `void`. The method communicates results through exceptions only — it returns nothing on success and throws on failure:
- `GuardianValidationException` — validation failed and no custom exception was provided
- `GuardianExecutingRuleException` — the underlying rule failed to execute (e.g. the value is not a string)
- the provided custom exception — validation failed and a custom exception was passed

Example:

```php
$jsonRuleGuardian->check('{"key":"value"}');
```

With custom exception:

```php
$jsonRuleGuardian->check('{invalid json}', new InvalidJsonException());
```

---

## 🏛️ Architecture

This package is a small shortcut layer over the Aegisora validation pipeline.

Flow:
1. `JsonRuleGuardian::check()` is called
2. `JsonRule::create()` is created
3. `Guardian` executes the rule
4. If validation succeeds, execution continues normally
5. If validation fails, custom exception or `GuardianValidationException` is thrown
6. If rule execution fails, `GuardianExecutingRuleException` is thrown

Internal flow:

```text
Value → JsonRuleGuardian → Guardian → JsonRule → Result → Exception
```

---

## 🔗 Related Packages

- [aegisora/guardian](https://github.com/Aegisora/guardian) — validation execution orchestrator
- [aegisora/json-rule](https://github.com/Aegisora/json-rule) — rule-based JSON string validation
- [aegisora/rule-contract](https://github.com/Aegisora/rule-contract) — base rule contract and validation result architecture

---

## ⚖️ License

This package is open-source and licensed under the MIT License. See the LICENSE for details.

---

## 🌱 Contributing

Contributions are welcome and greatly appreciated!. See the CONTRIBUTING for details.

---

## 🌟 Support

If you find this project useful, please consider giving it a star on GitHub!

It helps the project grow and motivates further development.
