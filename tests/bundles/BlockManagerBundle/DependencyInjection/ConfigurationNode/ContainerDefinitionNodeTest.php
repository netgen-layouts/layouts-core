<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

class ContainerDefinitionNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testContainerDefinitionSettings()
    {
        $config = array(
            array(
                'container_definitions' => array(
                    'container' => array(
                        'name' => 'Container',
                        'handler' => 'handler',
                        'forms' => array(
                            'full' => array(
                                'type' => 'test_form',
                                'enabled' => true,
                            ),
                        ),
                        'placeholder_forms' => array(
                            'full' => array(
                                'type' => 'test_form2',
                                'enabled' => true,
                            ),
                        ),
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                            ),
                            'view_2' => array(
                                'name' => 'View 2',
                                'enabled' => false,
                            ),
                            'view_3' => array(
                                'name' => 'View 3',
                                'enabled' => true,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'container_definitions' => array(
                'container' => array(
                    'name' => 'Container',
                    'handler' => 'handler',
                    'enabled' => true,
                    'forms' => array(
                        'full' => array(
                            'type' => 'test_form',
                            'enabled' => true,
                        ),
                    ),
                    'placeholder_forms' => array(
                        'full' => array(
                            'type' => 'test_form2',
                            'enabled' => true,
                        ),
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'enabled' => true,
                        ),
                        'view_2' => array(
                            'name' => 'Disabled',
                            'enabled' => false,
                        ),
                        'view_3' => array(
                            'name' => 'View 3',
                            'enabled' => true,
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'container_definitions'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testContainerDefinitionSettingsWithNoHandler()
    {
        $config = array(
            array(
                'container_definitions' => array(
                    'container' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'container_definitions' => array(
                'container' => array(),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'container_definitions.*.handler'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testContainerDefinitionSettingsWithDisabledContainerDefinition()
    {
        $config = array(
            array(
                'container_definitions' => array(
                    'container' => array(
                        'enabled' => false,
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'container_definitions' => array(
                'container' => array(
                    'enabled' => false,
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'container_definitions.*.enabled'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testContainerDefinitionSettingsViewTypesMerge()
    {
        $config = array(
            array(
                'container_definitions' => array(
                    'container' => array(
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                            ),
                            'view_2' => array(
                                'name' => 'View 2',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'container_definitions' => array(
                    'container' => array(
                        'view_types' => array(
                            'view_1' => array(
                                'name' => 'View 1',
                            ),
                            'view_2' => array(
                                'enabled' => false,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'container_definitions' => array(
                'container' => array(
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'enabled' => true,
                        ),
                        'view_1' => array(
                            'name' => 'View 1',
                            'enabled' => true,
                        ),
                        'view_2' => array(
                            'name' => 'Disabled',
                            'enabled' => false,
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'container_definitions.*.view_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerDefinitionNode::getConfigurationNode
     */
    public function testContainerDefinitionSettingsWithNoName()
    {
        $config = array(
            'container_definitions' => array(
                'container' => array(
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerDefinitionNode::getConfigurationNode
     */
    public function testContainerDefinitionSettingsWithNoContainerDefinitions()
    {
        $config = array(
            'container_definitions' => array(),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ContainerDefinitionNode::getConfigurationNode
     */
    public function testContainerDefinitionSettingsWithNoViewTypes()
    {
        $config = array(
            'container_definitions' => array(
                'container' => array(
                    'name' => 'Container',
                    'view_types' => array(),
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
