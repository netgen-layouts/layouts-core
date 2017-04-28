<?php

namespace Netgen\BlockManager\Tests\Core\Values\Config;

use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Core\Values\Config\ConfigCollection;
use Netgen\BlockManager\Exception\Core\ConfigException;
use PHPUnit\Framework\TestCase;

class ConfigCollectionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigCollection::getConfigType
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigCollection::getConfigs
     */
    public function testSetDefaultProperties()
    {
        $configCollection = new ConfigCollection();

        $this->assertNull($configCollection->getConfigType());
        $this->assertEquals(array(), $configCollection->getConfigs());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigCollection::getConfigType
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigCollection::getConfigs
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigCollection::getConfig
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigCollection::hasConfig
     */
    public function testSetProperties()
    {
        $configCollection = new ConfigCollection(
            array(
                'configType' => 'type',
                'configs' => array(
                    'config' => new Config(),
                ),
            )
        );

        $this->assertEquals('type', $configCollection->getConfigType());
        $this->assertEquals(array('config' => new Config()), $configCollection->getConfigs());
        $this->assertTrue($configCollection->hasConfig('config'));
        $this->assertFalse($configCollection->hasConfig('unknown'));
        $this->assertEquals(new Config(), $configCollection->getConfig('config'));

        try {
            $configCollection->getConfig('unknown');
        } catch (ConfigException $e) {
            // Do nothing
        }
    }
}
