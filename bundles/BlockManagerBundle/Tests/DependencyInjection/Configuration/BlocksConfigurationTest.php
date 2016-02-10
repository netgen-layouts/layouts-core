<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Configuration;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;

class BlocksConfigurationTest extends \PHPUnit_Framework_TestCase
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testBlockSettings()
    {
        $config = array(
            array(
                'blocks' => array(
                    'block' => array(
                        'forms' => array(
                            'full' => 'block_update',
                        ),
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                            ),
                            'large' => array(
                                'name' => 'Large',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'blocks' => array(
                'block' => array(
                    'forms' => array(
                        'full' => 'block_update',
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                        ),
                        'large' => array(
                            'name' => 'Large',
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'blocks'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testBlockSettingsWithNoFullForm()
    {
        $config = array(
            array(
                'blocks' => array(
                    'block' => array(
                        'forms' => array(),
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                            ),
                            'large' => array(
                                'name' => 'Large',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'blocks' => array(
                'block' => array(
                    'forms' => array(
                        'full' => 'block_update',
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                        ),
                        'large' => array(
                            'name' => 'Large',
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'blocks'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testBlockSettingsNoViewTypesMerge()
    {
        $config = array(
            array(
                'blocks' => array(
                    'block' => array(
                        'forms' => array(
                            'full' => 'block_update',
                        ),
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                            ),
                            'large' => array(
                                'name' => 'Large',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'blocks' => array(
                    'block' => array(
                        'forms' => array(
                            'full' => 'block_update',
                        ),
                        'view_types' => array(
                            'title' => array(
                                'name' => 'Title',
                            ),
                            'image' => array(
                                'name' => 'Image',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'blocks' => array(
                'block' => array(
                    'forms' => array(
                        'full' => 'block_update',
                    ),
                    'view_types' => array(
                        'title' => array(
                            'name' => 'Title',
                        ),
                        'image' => array(
                            'name' => 'Image',
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'blocks'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testBlockSettingsWithNoBlocks()
    {
        $config = array(
            'blocks' => array(),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testBlockSettingsWithNoForms()
    {
        $config = array(
            'blocks' => array(
                'block' => array(),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testBlockSettingsWithInvalidForms()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'forms' => 'forms',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testBlockSettingsWithEmptyFullForm()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'forms' => array(
                        'full' => '',
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testBlockSettingsWithInvalidFullForm()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'forms' => array(
                        'full' => array(),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testBlockSettingsWithEmptyFormInline()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'forms' => array(
                        'inline' => '',
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testBlockSettingsWithInvalidFormInline()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'forms' => array(
                        'inline' => array(),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testBlockSettingsWithNoViewTypes()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'forms' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testBlockSettingsWithEmptyViewTypes()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'forms' => array(),
                    'view_types' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testBlockSettingsWithInvalidViewTypes()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'forms' => array(),
                    'view_types' => 'default',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testBlockSettingsWithInvalidViewTypeItem()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'forms' => array(),
                    'view_types' => array(
                        'default' => 'default',
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testBlockSettingsWithNoViewTypeItemName()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'forms' => array(),
                    'view_types' => array(
                        'default' => array(),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testBlockSettingsWithEmptyViewTypeItemName()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'forms' => array(),
                    'view_types' => array(
                        'default' => array(
                            'name' => '',
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testBlockSettingsWithInvalidViewTypeItemName()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'forms' => array(),
                    'view_types' => array(
                        'default' => array(
                            'name' => array(),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }
}
