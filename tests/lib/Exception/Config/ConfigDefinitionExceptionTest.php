<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Config;

use Netgen\Layouts\Exception\Config\ConfigDefinitionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConfigDefinitionException::class)]
final class ConfigDefinitionExceptionTest extends TestCase
{
    public function testNoConfigDefinition(): void
    {
        $exception = ConfigDefinitionException::noConfigDefinition('key');

        self::assertSame(
            'Config definition with "key" config key does not exist.',
            $exception->getMessage(),
        );
    }
}
