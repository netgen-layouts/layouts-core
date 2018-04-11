<?php

namespace Netgen\BlockManager\Tests\Core\Values\Config;

use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Core\Values\Config\Config;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Config\Config::getConfigKey
     * @covers \Netgen\BlockManager\Core\Values\Config\Config::getDefinition
     */
    public function testSetDefaultProperties()
    {
        $config = new Config();

        $this->assertNull($config->getConfigKey());
        $this->assertNull($config->getDefinition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Config\Config::getConfigKey
     * @covers \Netgen\BlockManager\Core\Values\Config\Config::getDefinition
     */
    public function testSetProperties()
    {
        $definition = new ConfigDefinition();

        $config = new Config(
            array(
                'configKey' => 'config',
                'definition' => $definition,
            )
        );

        $this->assertEquals('config', $config->getConfigKey());
        $this->assertEquals($definition, $config->getDefinition());
    }
}
