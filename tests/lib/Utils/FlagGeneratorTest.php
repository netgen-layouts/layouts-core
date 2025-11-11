<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Utils;

use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Utils\FlagGenerator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FlagGenerator::class)]
final class FlagGeneratorTest extends TestCase
{
    public function testFromCountryCode(): void
    {
        self::assertSame('&#x1F1ED;&#x1F1F7;', FlagGenerator::fromCountryCode('hr'));
    }

    public function testFromCountryCodeThrowsRuntimeExceptionOnInvalidCountryCode(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid country code: invalid.');

        FlagGenerator::fromCountryCode('invalid');
    }

    public function testFromCountryCodeThrowsRuntimeExceptionOnNonSupporterCharacter(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid country code: dž.');

        FlagGenerator::fromCountryCode('dž');
    }
}
