<?php

namespace Aegisora\RuleGuardians\JsonRule\Tests\Unit;

use Aegisora\Guardian\Exceptions\GuardianExecutingRuleException;
use Aegisora\Guardian\Exceptions\GuardianValidationException;
use Aegisora\Guardian\Guardian;
use Aegisora\RuleGuardians\JsonRule\JsonRuleGuardian;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;

class JsonRuleGuardianTest extends TestCase
{
    private JsonRuleGuardian $guardian;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guardian = new JsonRuleGuardian(
            new Guardian()
        );
    }

    /**
     * @dataProvider getSuccessfullyCheckProvidedData
     */
    public function testSuccessfullyCheck(
        string $value
    ): void {
        $this->expectNotToPerformAssertions();

        $this->guardian->check($value);
    }

    public static function getSuccessfullyCheckProvidedData(): array
    {
        return [
            'value - empty object' => [
                'value' => '{}',
            ],
            'value - empty array' => [
                'value' => '[]',
            ],
            'value - object' => [
                'value' => '{"key":"value"}',
            ],
            'value - nested object' => [
                'value' => '{"key":{"nested":[1,2,3]}}',
            ],
            'value - array' => [
                'value' => '[1,2,3]',
            ],
            'value - string' => [
                'value' => '"foo"',
            ],
            'value - integer' => [
                'value' => '123',
            ],
            'value - zero' => [
                'value' => '0',
            ],
            'value - float' => [
                'value' => '1.1',
            ],
            'value - boolean true' => [
                'value' => 'true',
            ],
            'value - boolean false' => [
                'value' => 'false',
            ],
            'value - null' => [
                'value' => 'null',
            ],
        ];
    }

    /**
     * @dataProvider getFailedCheckProvidedData
     */
    public function testFailedCheck(
        string $value,
        ?Throwable $customRuleValidationException,
        string $expectedExceptionClassName
    ): void {
        $this->expectException($expectedExceptionClassName);

        $this->guardian->check($value, $customRuleValidationException);
    }

    public static function getFailedCheckProvidedData(): array
    {
        return [
            'value - empty string, custom rule validation exception - null' => [
                'value' => '',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - empty string, custom rule validation exception - not null' => [
                'value' => '',
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - whitespace string, custom rule validation exception - null' => [
                'value' => '   ',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - whitespace string, custom rule validation exception - not null' => [
                'value' => '   ',
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - not empty string, custom rule validation exception - null' => [
                'value' => 'foo',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - not empty string, custom rule validation exception - not null' => [
                'value' => 'foo',
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - not closed object, custom rule validation exception - null' => [
                'value' => '{',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - not closed object, custom rule validation exception - not null' => [
                'value' => '{',
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - object without value, custom rule validation exception - null' => [
                'value' => '{"key":}',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - object without value, custom rule validation exception - not null' => [
                'value' => '{"key":}',
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - object with unquoted key, custom rule validation exception - null' => [
                'value' => '{key:1}',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - object with unquoted key, custom rule validation exception - not null' => [
                'value' => '{key:1}',
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - object with trailing comma, custom rule validation exception - null' => [
                'value' => '{"key":1,}',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - object with trailing comma, custom rule validation exception - not null' => [
                'value' => '{"key":1,}',
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - not closed array, custom rule validation exception - null' => [
                'value' => '[1,2,',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - not closed array, custom rule validation exception - not null' => [
                'value' => '[1,2,',
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
        ];
    }

    /**
     * @dataProvider getFailedCheckWithDefaultCustomExceptionProvidedData
     */
    public function testFailedCheckWithDefaultCustomException(
        string $value
    ): void {
        $this->expectException(GuardianValidationException::class);

        try {
            $this->guardian->check($value);
        } catch (GuardianValidationException $exception) {
            self::assertSame('json_rule', $exception->getRuleCode());
            throw $exception;
        }
    }

    public static function getFailedCheckWithDefaultCustomExceptionProvidedData(): array
    {
        return [
            'value - empty string' => [
                'value' => '',
            ],
            'value - whitespace string' => [
                'value' => '   ',
            ],
            'value - not empty string' => [
                'value' => 'foo',
            ],
            'value - not closed object' => [
                'value' => '{',
            ],
            'value - object without value' => [
                'value' => '{"key":}',
            ],
            'value - object with unquoted key' => [
                'value' => '{key:1}',
            ],
            'value - object with trailing comma' => [
                'value' => '{"key":1,}',
            ],
            'value - not closed array' => [
                'value' => '[1,2,',
            ],
        ];
    }

    /**
     * @dataProvider getFailedCheckCauseValueIsNotStringProvidedData
     * @param mixed $value
     */
    public function testFailedCheckCauseValueIsNotStringThrowsGuardianExecutingRuleException(
        $value,
        ?Throwable $customRuleValidationException
    ): void {
        $this->expectException(GuardianExecutingRuleException::class);

        $this->guardian->check($value, $customRuleValidationException);
    }

    public static function getFailedCheckCauseValueIsNotStringProvidedData(): array
    {
        return [
            'value - null, custom rule validation exception - null' => [
                'value' => null,
                'customRuleValidationException' => null,
            ],
            'value - null, custom rule validation exception - not null' => [
                'value' => null,
                'customRuleValidationException' => new CustomRuleException(),
            ],
            'value - integer, custom rule validation exception - null' => [
                'value' => 1,
                'customRuleValidationException' => null,
            ],
            'value - float, custom rule validation exception - null' => [
                'value' => 1.1,
                'customRuleValidationException' => null,
            ],
            'value - boolean, custom rule validation exception - null' => [
                'value' => true,
                'customRuleValidationException' => null,
            ],
            'value - array, custom rule validation exception - null' => [
                'value' => [1, 2, 3],
                'customRuleValidationException' => null,
            ],
            'value - object, custom rule validation exception - null' => [
                'value' => new stdClass(),
                'customRuleValidationException' => null,
            ],
            'value - object, custom rule validation exception - not null' => [
                'value' => new stdClass(),
                'customRuleValidationException' => new CustomRuleException(),
            ],
        ];
    }

    public function testFailedCheckCauseGuardianThrowsGuardianExecutingRuleException(): void
    {
        $this->expectException(GuardianExecutingRuleException::class);

        $guardian = new JsonRuleGuardian(
            $this->getGuardianThrowsExceptionOnCheck(GuardianExecutingRuleException::class)
        );

        $guardian->check('');
    }

    public function testFailedCheckCauseGuardianThrowsNotExpectedException(): void
    {
        $this->expectException(Throwable::class);

        $guardian = new JsonRuleGuardian(
            $this->getGuardianThrowsExceptionOnCheck(Throwable::class)
        );

        $guardian->check('');
    }

    /**
     * @return Guardian|MockObject
     */
    private function getGuardianThrowsExceptionOnCheck(string $expectedExceptionClass): Guardian
    {
        $guardian = $this->getGuardianMock();

        $guardian
            ->expects(self::once())
            ->method('check')
            ->willThrowException($this->createMock($expectedExceptionClass));

        return $guardian;
    }

    /**
     * @return Guardian|MockObject
     */
    private function getGuardianMock(): Guardian
    {
        /** @var Guardian|MockObject $mock */
        $mock = $this->createMock(Guardian::class);

        return $mock;
    }
}
