<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Configuration;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;

class SourcesConfigurationTest extends \PHPUnit\Framework\TestCase
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getSourcesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getSourcesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getSourcesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testSourceSettingsWithNoQueriesMerge()
    {
        $config = array(
            array(
                'sources' => array(
                    'dynamic' => array(
                        'name' => 'Dynamic',
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
                        'name' => 'Dynamic',
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
                    'name' => 'Dynamic',
                    'enabled' => true,
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
            'sources'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getSourcesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testSourceSettingsNoParametersMerge()
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
                        'name' => 'Dynamic',
                        'queries' => array(
                            'default' => array(
                                'query_type' => 'type',
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
                    'name' => 'Dynamic',
                    'enabled' => true,
                    'queries' => array(
                        'default' => array(
                            'query_type' => 'type',
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
            'sources'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getSourcesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testSourceSettingsWithNoDefaultParameters()
    {
        $config = array(
            array(
                'sources' => array(
                    'dynamic' => array(
                        'name' => 'Dynamic',
                        'queries' => array(
                            'default' => array(
                                'query_type' => 'type',
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
                            'default_parameters' => array(),
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getSourcesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getSourcesNodeDefinition
     */
    public function testSourceSettingsWithEmptySources()
    {
        $config = array('sources' => array());
        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getSourcesNodeDefinition
     */
    public function testSourceSettingsWithNoName()
    {
        $config = array('sources' => array('dynamic' => array()));
        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getSourcesNodeDefinition
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getSourcesNodeDefinition
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getSourcesNodeDefinition
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
}
