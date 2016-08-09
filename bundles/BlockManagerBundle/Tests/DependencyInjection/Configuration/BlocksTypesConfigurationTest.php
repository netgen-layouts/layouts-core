<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Configuration;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class BlocksTypesConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        $extension = new NetgenBlockManagerExtension();

        return new Configuration($extension->getAlias());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testBlockTypeSettings()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'name' => 'Block type',
                        'definition_identifier' => 'title',
                        'defaults' => array(
                            'name' => 'Name',
                            'view_type' => 'large',
                            'item_view_type' => 'standard',
                            'parameters' => array(
                                'param1' => 'value1',
                                'param2' => 'value2',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'name' => 'Block type',
                    'enabled' => true,
                    'definition_identifier' => 'title',
                    'defaults' => array(
                        'name' => 'Name',
                        'view_type' => 'large',
                        'item_view_type' => 'standard',
                        'parameters' => array(
                            'param1' => 'value1',
                            'param2' => 'value2',
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testBlockTypeSettingsWithNoDefaultName()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'defaults' => array(),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'defaults' => array(
                        'name' => '',
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.defaults.name'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testBlockTypeSettingsWithNoDefaultViewType()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'defaults' => array(),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'defaults' => array(
                        'view_type' => '',
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.defaults.view_type'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testBlockTypeSettingsWithNoDefaultItemViewType()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'defaults' => array(),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'defaults' => array(
                        'item_view_type' => '',
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.defaults.item_view_type'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testBlockTypeSettingsWithNoDefaultParameters()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'defaults' => array(),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'defaults' => array(
                        'parameters' => array(),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.defaults.parameters'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testBlockTypeSettingsWithNoParametersMerge()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'defaults' => array(
                            'parameters' => array(
                                'param1' => 'value1',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'block_types' => array(
                    'block_type' => array(
                        'defaults' => array(
                            'parameters' => array(
                                'param2' => 'value2',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'defaults' => array(
                        'parameters' => array(
                            'param2' => 'value2',
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.defaults.parameters'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     */
    public function testBlockTypeSettingsWithNoDefaults()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'defaults' => array(
                        'name' => '',
                        'view_type' => '',
                        'item_view_type' => '',
                        'parameters' => array(),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.defaults'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     */
    public function testBlockTypeSettingsWithNoBlockTypes()
    {
        $config = array(
            'block_types' => array(),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     */
    public function testBlockTypeSettingsWithNoBlockTypeName()
    {
        $config = array(
            'block_types' => array(
                'block_type' => array(
                    'definition_identifier' => 'block',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     */
    public function testBlockTypeSettingsWithNoDefaultDefinitionIdentifier()
    {
        $config = array(
            'block_types' => array(
                'block_type' => array(
                    'name' => 'Block type',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }
}
