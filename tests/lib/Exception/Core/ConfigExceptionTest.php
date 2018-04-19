<?php

namespace Netgen\BlockManager\Tests\Exception\Core;

use Netgen\BlockManager\Exception\Core\ConfigException;
use PHPUnit\Framework\TestCase;

final class ConfigExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Core\ConfigException::noConfig
     */
    public function testNoConfig()
    {
        $exception = ConfigException::noConfig('config');

        $this->assertEquals(
            'Configuration with "config" config key does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Core\ConfigException::noConfigStruct
     */
    public function testNoConfigStruct()
    {
        $exception = ConfigException::noConfigStruct('config');

        $this->assertEquals(
            'Config struct with config key "config" does not exist.',
            $exception->getMessage()
        );
    }
}
