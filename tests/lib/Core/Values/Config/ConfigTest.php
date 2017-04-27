<?php

namespace Netgen\BlockManager\Tests\Core\Values\Config;

use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Tests\Config\Stubs\Block\HttpCacheConfigHandler;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinition;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Config\Config::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Config\Config::getDefinition
     */
    public function testSetDefaultProperties()
    {
        $config = new Config();

        $this->assertNull($config->getIdentifier());
        $this->assertNull($config->getDefinition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Config\Config::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Config\Config::getDefinition
     */
    public function testSetProperties()
    {
        $definition = new ConfigDefinition(
            'block',
            'config',
            new HttpCacheConfigHandler()
        );

        $config = new Config(
            array(
                'identifier' => 'config',
                'definition' => $definition,
            )
        );

        $this->assertEquals('config', $config->getIdentifier());
        $this->assertEquals($definition, $config->getDefinition());
    }
}
