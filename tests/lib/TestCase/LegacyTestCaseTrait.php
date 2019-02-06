<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\TestCase;

use PHPUnit\Framework\Assert;

trait LegacyTestCaseTrait
{
    public static function assertIsArray($actual, string $message = ''): void
    {
        if (method_exists(Assert::class, 'assertIsArray')) {
            Assert::assertIsArray($actual, $message);

            return;
        }

        Assert::assertInternalType('array', $actual, $message);
    }

    public static function assertIsBool($actual, string $message = ''): void
    {
        if (method_exists(Assert::class, 'assertIsBool')) {
            Assert::assertIsBool($actual, $message);

            return;
        }

        Assert::assertInternalType('bool', $actual, $message);
    }

    public static function assertIsFloat($actual, string $message = ''): void
    {
        if (method_exists(Assert::class, 'assertIsFloat')) {
            Assert::assertIsFloat($actual, $message);

            return;
        }

        Assert::assertInternalType('float', $actual, $message);
    }

    public static function assertIsInt($actual, string $message = ''): void
    {
        if (method_exists(Assert::class, 'assertIsInt')) {
            Assert::assertIsInt($actual, $message);

            return;
        }

        Assert::assertInternalType('int', $actual, $message);
    }

    public static function assertIsNumeric($actual, string $message = ''): void
    {
        if (method_exists(Assert::class, 'assertIsNumeric')) {
            Assert::assertIsNumeric($actual, $message);

            return;
        }

        Assert::assertInternalType('numeric', $actual, $message);
    }

    public static function assertIsObject($actual, string $message = ''): void
    {
        if (method_exists(Assert::class, 'assertIsObject')) {
            Assert::assertIsObject($actual, $message);

            return;
        }

        Assert::assertInternalType('object', $actual, $message);
    }

    public static function assertIsResource($actual, string $message = ''): void
    {
        if (method_exists(Assert::class, 'assertIsResource')) {
            Assert::assertIsResource($actual, $message);

            return;
        }

        Assert::assertInternalType('resource', $actual, $message);
    }

    public static function assertIsString($actual, string $message = ''): void
    {
        if (method_exists(Assert::class, 'assertIsString')) {
            Assert::assertIsString($actual, $message);

            return;
        }

        Assert::assertInternalType('string', $actual, $message);
    }

    public static function assertIsScalar($actual, string $message = ''): void
    {
        if (method_exists(Assert::class, 'assertIsScalar')) {
            Assert::assertIsScalar($actual, $message);

            return;
        }

        Assert::assertInternalType('scalar', $actual, $message);
    }

    public static function assertIsCallable($actual, string $message = ''): void
    {
        if (method_exists(Assert::class, 'assertIsCallable')) {
            Assert::assertIsCallable($actual, $message);

            return;
        }

        Assert::assertInternalType('callable', $actual, $message);
    }

    public static function assertIsIterable($actual, string $message = ''): void
    {
        if (method_exists(Assert::class, 'assertIsIterable')) {
            Assert::assertIsIterable($actual, $message);

            return;
        }

        Assert::assertInternalType('iterable', $actual, $message);
    }
}
