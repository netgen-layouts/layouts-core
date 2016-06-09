<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Configuration;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class LayoutTypesConfigurationTest extends TestCase
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutTypesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testLayoutTypeSettings()
    {
        $config = array(
            array(
                'layout_types' => array(
                    'layout' => array(
                        'name' => 'layout',
                        'zones' => array(
                            'zone' => array(
                                'name' => 'zone',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'layout_types' => array(
                'layout' => array(
                    'name' => 'layout',
                    'enabled' => true,
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_block_definitions' => array(),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutTypesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testLayoutTypeSettingsNoZonesMerge()
    {
        $config = array(
            array(
                'layout_types' => array(
                    'layout' => array(
                        'name' => 'layout',
                        'zones' => array(
                            'left' => array(
                                'name' => 'Left',
                            ),
                            'right' => array(
                                'name' => 'Right',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'layout_types' => array(
                    'layout' => array(
                        'name' => 'layout',
                        'zones' => array(
                            'top' => array(
                                'name' => 'Top',
                            ),
                            'bottom' => array(
                                'name' => 'Bottom',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'layout_types' => array(
                'layout' => array(
                    'name' => 'layout',
                    'enabled' => true,
                    'zones' => array(
                        'top' => array(
                            'name' => 'Top',
                            'allowed_block_definitions' => array(),
                        ),
                        'bottom' => array(
                            'name' => 'Bottom',
                            'allowed_block_definitions' => array(),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutTypesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testLayoutTypeSettingsWithAllowedBlockDefinitions()
    {
        $config = array(
            array(
                'layout_types' => array(
                    'layout' => array(
                        'name' => 'layout',
                        'zones' => array(
                            'zone' => array(
                                'name' => 'zone',
                                'allowed_block_definitions' => array('title', 'paragraph'),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'layout_types' => array(
                'layout' => array(
                    'name' => 'layout',
                    'enabled' => true,
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_block_definitions' => array('title', 'paragraph'),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutTypesNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testLayoutTypeSettingsWithNonUniqueAllowedBlockDefinitions()
    {
        $config = array(
            array(
                'layout_types' => array(
                    'layout' => array(
                        'name' => 'layout',
                        'zones' => array(
                            'zone' => array(
                                'name' => 'zone',
                                'allowed_block_definitions' => array('title', 'paragraph', 'title'),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'layout_types' => array(
                'layout' => array(
                    'name' => 'layout',
                    'enabled' => true,
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_block_definitions' => array('title', 'paragraph'),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutTypesNodeDefinition
     */
    public function testLayoutTypeSettingsWithEmptyLayouts()
    {
        $config = array('layout_types' => array());
        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutTypesNodeDefinition
     */
    public function testLayoutTypeSettingsWithNoName()
    {
        $config = array('layout_types' => array('layout' => array()));
        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutTypesNodeDefinition
     */
    public function testLayoutTypeSettingsWithNoZones()
    {
        $config = array(
            'layout_types' => array(
                'layout' => array(
                    'name' => 'layout',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutTypesNodeDefinition
     */
    public function testLayoutTypeSettingsWithEmptyZones()
    {
        $config = array(
            'layout_types' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutTypesNodeDefinition
     */
    public function testLayoutTypeSettingsWithNoZoneName()
    {
        $config = array(
            'layout_types' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutTypesNodeDefinition
     */
    public function testLayoutTypeSettingsWithEmptyAllowedBlockDefinitions()
    {
        $config = array(
            'layout_types' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'name' => 'zone',
                        'allowed_block_definitions' => array(),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }
}
