<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

class ContainerTypeNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testContainerTypeSettings()
    {
        $config = array(
            array(
                'container_types' => array(
                    'container_type' => array(
                        'name' => 'Container',
                        'definition_identifier' => 'container',
                        'defaults' => array(
                            'name' => 'Name',
                            'view_type' => 'container_view',
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
            'container_types' => array(
                'container_type' => array(
                    'name' => 'Container',
                    'enabled' => true,
                    'definition_identifier' => 'container',
                    'defaults' => array(
                        'name' => 'Name',
                        'view_type' => 'container_view',
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
            'container_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testContainerTypeSettingsWithNoName()
    {
        $config = array(
            array(
                'container_types' => array(
                    'container_type' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'container_types' => array(
                'container_type' => array(),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'container_types.*.name'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testContainerTypeSettingsWithNoNameAndWithDefinitionIdentifierWhenDisabled()
    {
        $config = array(
            array(
                'container_types' => array(
                    'container_type' => array(
                        'enabled' => false,
                        'definition_identifier' => 'container',
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'container_types' => array(
                'container_type' => array(
                    'enabled' => false,
                    'definition_identifier' => 'container',
                    'defaults' => array(
                        'name' => '',
                        'view_type' => '',
                        'parameters' => array(),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'container_types.*'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testContainerTypeSettingsWithNoDefinitionIdentifier()
    {
        $config = array(
            array(
                'container_types' => array(
                    'container_type' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'container_types' => array(
                'container_type' => array(),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'container_types.*.definition_identifier'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testContainerTypeSettingsWithNoDefaultName()
    {
        $config = array(
            array(
                'container_types' => array(
                    'container_type' => array(
                        'defaults' => array(),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'container_types' => array(
                'container_type' => array(
                    'defaults' => array(
                        'name' => '',
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'container_types.*.defaults.name'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testContainerTypeSettingsWithNoDefaultViewType()
    {
        $config = array(
            array(
                'container_types' => array(
                    'container_type' => array(
                        'defaults' => array(),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'container_types' => array(
                'container_type' => array(
                    'defaults' => array(
                        'view_type' => '',
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'container_types.*.defaults.view_type'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testContainerTypeSettingsWithNoDefaultParameters()
    {
        $config = array(
            array(
                'container_types' => array(
                    'container_type' => array(
                        'defaults' => array(),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'container_types' => array(
                'container_type' => array(
                    'defaults' => array(
                        'parameters' => array(),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'container_types.*.defaults.parameters'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testContainerTypeSettingsWithNoParametersMerge()
    {
        $config = array(
            array(
                'container_types' => array(
                    'container_type' => array(
                        'defaults' => array(
                            'parameters' => array(
                                'param1' => 'value1',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'container_types' => array(
                    'container_type' => array(
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
            'container_types' => array(
                'container_type' => array(
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
            'container_types.*.defaults.parameters'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerTypeNode::getConfigurationNode
     */
    public function testContainerTypeSettingsWithNoDefaults()
    {
        $config = array(
            array(
                'container_types' => array(
                    'container_type' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'container_types' => array(
                'container_type' => array(
                    'defaults' => array(
                        'name' => '',
                        'view_type' => '',
                        'parameters' => array(),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'container_types.*.defaults'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerTypeNode::getConfigurationNode
     */
    public function testContainerTypeSettingsWithNoContainerTypes()
    {
        $config = array(
            'container_types' => array(),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerTypeNode::getConfigurationNode
     */
    public function testContainerTypeSettingsWithNoNameAndWithDefinitionIdentifier()
    {
        $config = array(
            'container_types' => array(
                'container_type' => array(
                    'definition_identifier' => 'container',
                ),
            ),
        );

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
