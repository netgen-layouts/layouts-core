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
     * @covers \Netgen\BlockManager\Exception\Core\ConfigException::configNotEnabled
     */
    public function testConfigNotEnabled()
    {
        $exception = ConfigException::configNotEnabled('config');

        $this->assertEquals(
            'Config with "config" config key is not enabled.',
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
