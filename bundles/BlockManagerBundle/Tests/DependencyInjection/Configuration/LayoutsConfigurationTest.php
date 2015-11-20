<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Configuration;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit_Framework_TestCase;

class LayoutsConfigurationTest extends PHPUnit_Framework_TestCase
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testLayoutSettings()
    {
        $config = array(
            array(
                'layouts' => array(
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
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_blocks' => array(),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layouts'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testLayoutSettingsNoZonesMerge()
    {
        $config = array(
            array(
                'layouts' => array(
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
                'layouts' => array(
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
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'top' => array(
                            'name' => 'Top',
                            'allowed_blocks' => array(),
                        ),
                        'bottom' => array(
                            'name' => 'Bottom',
                            'allowed_blocks' => array(),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layouts'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testLayoutSettingsWithAllowedBlocks()
    {
        $config = array(
            array(
                'layouts' => array(
                    'layout' => array(
                        'name' => 'layout',
                        'zones' => array(
                            'zone' => array(
                                'name' => 'zone',
                                'allowed_blocks' => array('title', 'paragraph'),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_blocks' => array('title', 'paragraph'),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layouts'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testLayoutSettingsWithNonUniqueAllowedBlocks()
    {
        $config = array(
            array(
                'layouts' => array(
                    'layout' => array(
                        'name' => 'layout',
                        'zones' => array(
                            'zone' => array(
                                'name' => 'zone',
                                'allowed_blocks' => array('title', 'paragraph', 'title'),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_blocks' => array('title', 'paragraph'),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layouts'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testLayoutSettingsWithEmptyLayouts()
    {
        $config = array('layouts' => array());
        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testLayoutSettingsWithInvalidLayouts()
    {
        $config = array('layouts' => 'layouts');
        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testLayoutSettingsWithNoName()
    {
        $config = array('layouts' => array('layout' => array()));
        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testLayoutSettingsWithEmptyName()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => '',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testLayoutSettingsWithInvalidName()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testLayoutSettingsWithNoZones()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testLayoutSettingsWithEmptyZones()
    {
        $config = array(
            'layouts' => array(
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testLayoutSettingsWithInvalidZones()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => 'zone',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testLayoutSettingsWithNoZoneName()
    {
        $config = array(
            'layouts' => array(
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testLayoutSettingsWithEmptyZoneName()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'name' => '',
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testLayoutSettingsWithInvalidZoneName()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'name' => array(),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testLayoutSettingsWithEmptyAllowedBlocks()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'name' => 'zone',
                        'allowed_blocks' => array(),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testLayoutSettingsWithInvalidAllowedBlocks()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'name' => 'zone',
                        'allowed_blocks' => 'allowed_blocks',
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testLayoutSettingsWithEmptyAllowedBlockItem()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_blocks' => array(''),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testLayoutSettingsWithInvalidAllowedBlockItem()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_blocks' => array(array()),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }
}
