<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

final class ItemsNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ItemsNode::getConfigurationNode
     */
    public function testItemsSettings()
    {
        $config = array(
            array(
                'items' => array(
                    'value_types' => array(
                        'value1' => array(
                            'name' => 'Value 1',
                        ),
                        'value2' => array(
                            'enabled' => false,
                            'name' => 'Value 2',
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'items' => array(
                'value_types' => array(
                    'value1' => array(
                        'name' => 'Value 1',
                        'enabled' => true,
                    ),
                    'value2' => array(
                        'name' => 'Value 2',
                        'enabled' => false,
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'items.value_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ItemsNode::getConfigurationNode
     */
    public function testItemsSettingsWithNoValueTypes()
    {
        $config = array(array('items' => array()));

        $expectedConfig = array(
            'items' => array(
                'value_types' => array(),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'items.value_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ItemsNode::getConfigurationNode
     */
    public function testItemsSettingsWithEmptyValueTypes()
    {
        $config = array(array('items' => array('value_types' => array())));

        $expectedConfig = array(
            'items' => array(
                'value_types' => array(),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'items.value_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ItemsNode::getConfigurationNode
     */
    public function testValueTypesSettingsWithNoName()
    {
        $config = array(array('items' => array('value_types' => array('value' => array()))));
        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}
