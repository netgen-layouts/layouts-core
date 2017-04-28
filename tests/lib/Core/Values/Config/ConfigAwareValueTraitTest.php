<?php

namespace Netgen\BlockManager\Tests\Core\Values\Config;

use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Core\Values\Config\ConfigCollection;
use Netgen\BlockManager\Exception\Core\ConfigException;
use Netgen\BlockManager\Tests\Core\Stubs\ConfigAwareValue;
use PHPUnit\Framework\TestCase;

class ConfigAwareValueTraitTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait::getConfigCollection
     */
    public function testSetDefaultProperties()
    {
        $value = new ConfigAwareValue();

        $this->assertNull($value->getConfigCollection());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait::getConfigCollection
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait::getConfigs
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait::getConfig
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait::hasConfig
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

        $value = new ConfigAwareValue(
            array(
                'configCollection' => $configCollection,
            )
        );

        $this->assertEquals($configCollection, $value->getConfigCollection());
        $this->assertEquals(array('config' => new Config()), $value->getConfigs());
        $this->assertTrue($value->hasConfig('config'));
        $this->assertFalse($value->hasConfig('unknown'));
        $this->assertEquals(new Config(), $value->getConfig('config'));

        try {
            $value->getConfig('unknown');
        } catch (ConfigException $e) {
            // Do nothing
        }
    }
}
