<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Configuration;

use Netgen\BlockManager\Collection\Query\Form\FullEditType;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class QueryTypesConfigurationTest extends TestCase
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getQueryTypesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testQueryTypeSettings()
    {
        $config = array(
            array(
                'query_types' => array(
                    'type' => array(
                        'name' => 'Type',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getQueryTypesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getQueryTypesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getQueryTypesNodeDefinition
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getQueryTypesNodeDefinition
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getQueryTypesNodeDefinition
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getQueryTypesNodeDefinition
     */
    public function testQueryTypeSettingsWithNoDefaults()
    {
        $config = array(
            'query_types' => array(
                'type' => array(
                    'name' => 'Type',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }
}
