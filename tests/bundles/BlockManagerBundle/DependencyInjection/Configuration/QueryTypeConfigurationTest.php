<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Configuration;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

class QueryTypeConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testQueryTypeSettings()
    {
        $config = array(
            array(
                'query_types' => array(
                    'type' => array(
                        'name' => 'Type',
                        'handler' => 'handler',
                        'forms' => array(
                            'full' => array(
                                'type' => 'full_edit',
                                'enabled' => true,
                            ),
                        ),
                        'defaults' => array(
                            'parameters' => array(
                                'param' => 'value',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'query_types' => array(
                'type' => array(
                    'name' => 'Type',
                    'handler' => 'handler',
                    'forms' => array(
                        'full' => array(
                            'type' => 'full_edit',
                            'enabled' => true,
                        ),
                    ),
                    'defaults' => array(
                        'parameters' => array(
                            'param' => 'value',
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'query_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testQueryTypeSettingsWithNoHandler()
    {
        $config = array(
            array(
                'query_types' => array(
                    'type' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'query_types' => array(
                'type' => array(),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'query_types.*.handler'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testQueryTypeSettingsWithNoDefaultParameters()
    {
        $config = array(
            array(
                'query_types' => array(
                    'type' => array(
                        'defaults' => array(),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'query_types' => array(
                'type' => array(
                    'defaults' => array(
                        'parameters' => array(),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'query_types.*.defaults.parameters'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testQueryTypeSettingsWithNoParametersMerge()
    {
        $config = array(
            array(
                'query_types' => array(
                    'type' => array(
                        'defaults' => array(
                            'parameters' => array(
                                'param1' => 'value1',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'query_types' => array(
                    'type' => array(
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
            'query_types' => array(
                'type' => array(
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
            'query_types.*.defaults.parameters'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     */
    public function testQueryTypeSettingsWithNoDefaults()
    {
        $config = array(
            array(
                'query_types' => array(
                    'type' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'query_types' => array(
                'type' => array(
                    'defaults' => array(
                        'parameters' => array(),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'query_types.*.defaults.parameters'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     */
    public function testQueryTypeSettingsWithNoQueryTypes()
    {
        $config = array(
            'query_types' => array(),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     */
    public function testQueryTypeSettingsWithNoName()
    {
        $config = array(
            'query_types' => array(
                'type' => array(
                    'defaults' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     */
    public function testQueryTypeSettingsWithEmptyName()
    {
        $config = array(
            'query_types' => array(
                'type' => array(
                    'name' => '',
                    'defaults' => array(),
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
