<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

class SourceNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\SourceNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testSourceSettings()
    {
        $config = array(
            array(
                'sources' => array(
                    'dynamic' => array(
                        'name' => 'Dynamic',
                        'queries' => array(
                            'default' => array(
                                'query_type' => 'type',
                                'default_parameters' => array(
                                    'param' => 'value',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'sources' => array(
                'dynamic' => array(
                    'name' => 'Dynamic',
                    'enabled' => true,
                    'queries' => array(
                        'default' => array(
                            'query_type' => 'type',
                            'default_parameters' => array(
                                'param' => 'value',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'sources'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\SourceNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testSourceSettingsWithDefaultSource()
    {
        $config = array(
            array(
                'sources' => array(
                    'dynamic' => array(
                        'name' => 'Dynamic',
                        'query_type' => 'type',
                        'default_parameters' => array(
                            'param' => 'value',
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'sources' => array(
                'dynamic' => array(
                    'name' => 'Dynamic',
                    'enabled' => true,
                    'queries' => array(
                        'default' => array(
                            'query_type' => 'type',
                            'default_parameters' => array(
                                'param' => 'value',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'sources'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\SourceNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testSourceSettingsWithNoQueriesMerge()
    {
        $config = array(
            array(
                'sources' => array(
                    'dynamic' => array(
                        'queries' => array(
                            'default' => array(
                                'query_type' => 'type1',
                                'default_parameters' => array(
                                    'param1' => 'value1',
                                    'param2' => 'value2',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'sources' => array(
                    'dynamic' => array(
                        'queries' => array(
                            'default' => array(
                                'query_type' => 'type2',
                                'default_parameters' => array(
                                    'param3' => 'value3',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'sources' => array(
                'dynamic' => array(
                    'queries' => array(
                        'default' => array(
                            'query_type' => 'type2',
                            'default_parameters' => array(
                                'param3' => 'value3',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'sources.*.queries'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\SourceNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testSourceSettingsNoParametersMerge()
    {
        $config = array(
            array(
                'sources' => array(
                    'dynamic' => array(
                        'queries' => array(
                            'default' => array(
                                'default_parameters' => array(
                                    'param' => 'value',
                                    'param2' => 'value2',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'sources' => array(
                    'dynamic' => array(
                        'queries' => array(
                            'default' => array(
                                'default_parameters' => array(
                                    'param3' => 'value3',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'sources' => array(
                'dynamic' => array(
                    'queries' => array(
                        'default' => array(
                            'default_parameters' => array(
                                'param3' => 'value3',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'sources.*.queries.*.default_parameters'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\SourceNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testSourceSettingsWithNoDefaultParameters()
    {
        $config = array(
            array(
                'sources' => array(
                    'dynamic' => array(
                        'queries' => array(
                            'default' => array(),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'sources' => array(
                'dynamic' => array(
                    'queries' => array(
                        'default' => array(
                            'default_parameters' => array(),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'sources.*.queries.*.default_parameters'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\SourceNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testSourceSettingsWithEmptyDefaultParameters()
    {
        $config = array(
            'sources' => array(
                'dynamic' => array(
                    'name' => 'Dynamic',
                    'queries' => array(
                        'default' => array(
                            'query_type' => 'type',
                            'default_parameters' => array(),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\SourceNode::getConfigurationNode
     */
    public function testSourceSettingsWithEmptySources()
    {
        $config = array('sources' => array());
        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\SourceNode::getConfigurationNode
     */
    public function testSourceSettingsWithNoName()
    {
        $config = array('sources' => array('dynamic' => array()));
        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\SourceNode::getConfigurationNode
     */
    public function testSourceSettingsWithNoQueries()
    {
        $config = array(
            'sources' => array(
                'dynamic' => array(
                    'name' => 'Dynamic',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\SourceNode::getConfigurationNode
     */
    public function testSourceSettingsWithEmptyQueries()
    {
        $config = array(
            'sources' => array(
                'dynamic' => array(
                    'name' => 'Dynamic',
                    'queries' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\SourceNode::getConfigurationNode
     */
    public function testSourceSettingsWithNoQueryType()
    {
        $config = array(
            'sources' => array(
                'dynamic' => array(
                    'name' => 'Dynamic',
                    'queries' => array(
                        'default' => array(),
                    ),
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
