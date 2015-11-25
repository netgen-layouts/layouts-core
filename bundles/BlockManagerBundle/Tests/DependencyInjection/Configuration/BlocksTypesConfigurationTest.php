<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Configuration;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;

class BlocksTypesConfigurationTest extends \PHPUnit_Framework_TestCase
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
                        'type_name' => 'Block type',
                        'defaults' => array(
                            'name' => 'Name',
                            'definition_identifier' => 'title',
                            'view_type' => 'large',
                            'parameters' => array(
                                'param1' => 'value1',
                                'param2' => 'value2'
                            )
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => array(
                        'name' => 'Name',
                        'definition_identifier' => 'title',
                        'view_type' => 'large',
                            'parameters' => array(
                                'param1' => 'value1',
                                'param2' => 'value2'
                            )
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
                        'type_name' => 'Block type',
                        'defaults' => array(
                            'definition_identifier' => 'title',
                            'view_type' => 'large',
                            'parameters' => array(
                                'param1' => 'value1',
                                'param2' => 'value2'
                            )
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => array(
                        'name' => '',
                        'definition_identifier' => 'title',
                        'view_type' => 'large',
                        'parameters' => array(
                            'param1' => 'value1',
                            'param2' => 'value2'
                        )
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
    public function testBlockTypeSettingsWithNoDefaultParameters()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'type_name' => 'Block type',
                        'defaults' => array(
                            'name' => 'Name',
                            'definition_identifier' => 'title',
                            'view_type' => 'large',
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => array(
                        'name' => 'Name',
                        'definition_identifier' => 'title',
                        'view_type' => 'large',
                        'parameters' => array()
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
    public function testBlockTypeSettingsWithNoParametersMerge()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'type_name' => 'Block type',
                        'defaults' => array(
                            'name' => 'Name',
                            'definition_identifier' => 'title',
                            'view_type' => 'large',
                            'parameters' => array(
                                'param1' => 'value1'
                            )
                        ),
                    ),
                ),
            ),
            array(
                'block_types' => array(
                    'block_type' => array(
                        'type_name' => 'Block type',
                        'defaults' => array(
                            'name' => 'Name',
                            'definition_identifier' => 'title',
                            'view_type' => 'large',
                            'parameters' => array(
                                'param2' => 'value2'
                            )
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => array(
                        'name' => 'Name',
                        'definition_identifier' => 'title',
                        'view_type' => 'large',
                        'parameters' => array(
                            'param2' => 'value2'
                        )
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
                'block_type' => array(),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     */
    public function testBlockTypeSettingsWithEmptyBlockTypeName()
    {
        $config = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => ''
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     */
    public function testBlockTypeSettingsWithInvalidBlockTypeName()
    {
        $config = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => array()
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     */
    public function testBlockTypeSettingsWithNoDefaults()
    {
        $config = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type'
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     */
    public function testBlockTypeSettingsWithInvalidDefaults()
    {
        $config = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => 'defaults'
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     */
    public function testBlockTypeSettingsWithInvalidDefaultName()
    {
        $config = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => array(
                        'name' => array()
                    )
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     */
    public function testBlockTypeSettingsWithNoDefaultViewType()
    {
        $config = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     */
    public function testBlockTypeSettingsWithEmptyDefaultViewType()
    {
        $config = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => array(
                        'view_type' => ''
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     */
    public function testBlockTypeSettingsWithInvalidDefaultViewType()
    {
        $config = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => array(
                        'view_type' => array()
                    ),
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
                    'type_name' => 'Block type',
                    'defaults' => array(
                        'view_type' => 'large',
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     */
    public function testBlockTypeSettingsWithInvalidDefaultDefinitionIdentifier()
    {
        $config = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => array(
                        'view_type' => 'large',
                        'definition_identifier' => array(),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     */
    public function testBlockTypeSettingsWithEmptyDefaultDefinitionIdentifier()
    {
        $config = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => array(
                        'default' => array(
                        'view_type' => 'large',
                        'definition_identifier' => array(),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     */
    public function testBlockTypeSettingsWithInvalidParameters()
    {
        $config = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => array(
                        'view_type' => 'large',
                        'definition_identifier' => 'definition_identifier',
                        'parameters' => 'parameters'
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypesNodeDefinition
     */
    public function testBlockTypeSettingsWithEmptyParameters()
    {
        $config = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => array(
                        'view_type' => 'large',
                        'definition_identifier' => 'definition_identifier',
                        'parameters' => array()
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }
}
