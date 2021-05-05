<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\API;

use Netgen\Layouts\Exception\API\ConfigException;
use PHPUnit\Framework\TestCase;

final class ConfigExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\API\ConfigException::noConfig
     */
    public function testNoConfig(): void
    {
        $exception = ConfigException::noConfig('config');

        self::assertSame(
            'Configuration with "config" config key does not exist.',
            $exception->getMessage(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Exception\API\ConfigException::noConfigStruct
     */
    public function testNoConfigStruct(): void
    {
        $exception = ConfigException::noConfigStruct('config');

        self::assertSame(
            'Config struct with config key "config" does not exist.',
            $exception->getMessage(),
        );
    }
}
