<?php

namespace Netgen\BlockManager\Tests\Core\Values\Config;

use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Exception\Core\ConfigException;
use Netgen\BlockManager\Tests\Config\Stubs\Block\HttpCacheConfigHandler;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinition;
use Netgen\BlockManager\Tests\Core\Stubs\ConfigAwareValue;
use PHPUnit\Framework\TestCase;

final class ConfigAwareValueTraitTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait::getConfigs
     */
    public function testSetDefaultProperties()
    {
        $value = new ConfigAwareValue();

        $this->assertEquals(array(), $value->getConfigs());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait::getConfigs
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait::getConfig
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait::hasConfig
     */
    public function testSetProperties()
    {
        $value = new ConfigAwareValue(
            array(
                'configs' => array(
                    'config' => new Config(),
                ),
            )
        );

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

    /**
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait::isConfigEnabled
     */
    public function testIsConfigEnabled()
    {
        $value = new ConfigAwareValue(
            array(
                'configs' => array(
                    'config' => new Config(
                        array(
                            'definition' => new ConfigDefinition(
                                'config',
                                new HttpCacheConfigHandler()
                            ),
                        )
                    ),
                ),
            )
        );

        $this->assertTrue($value->isConfigEnabled('config'));
    }
}
