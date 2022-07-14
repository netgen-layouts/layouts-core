<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

use PHPUnit\Framework\Assert;

use function method_exists;

trait LegacyTestCaseTrait
{
    public static function assertMatchesRegularExpression(string $pattern, string $string, string $message = ''): void
    {
        if (method_exists(Assert::class, 'assertMatchesRegularExpression')) {
            Assert::assertMatchesRegularExpression($pattern, $string, $message);

            return;
        }

        Assert::assertRegExp($pattern, $string, $message);
    }
}
