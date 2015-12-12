<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Configuration;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;

class BlockTypeGroupsConfigurationTest extends \PHPUnit_Framework_TestCase
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
     */
    public function testBlockTypeGroupsSettings()
    {
        $config = array(
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'name' => 'block_type_group',
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'blocks' => array(),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_type_groups'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
     */
    public function testBlockTypeGroupsSettingsWithBlocksMerge()
    {
        $config = array(
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'name' => 'block_type_group',
                        'blocks' => array('title', 'paragraph'),
                    ),
                ),
            ),
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'name' => 'block_type_group',
                        'blocks' => array('image'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'blocks' => array('title', 'paragraph', 'image'),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_type_groups'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
     */
    public function testBlockTypeGroupsSettingsWithBlocks()
    {
        $config = array(
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'name' => 'block_type_group',
                        'blocks' => array('title', 'image'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'blocks' => array('title', 'image'),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_type_groups'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
     */
    public function testBlockTypeGroupsSettingsWithNonUniqueBlocks()
    {
        $config = array(
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'name' => 'block_type_group',
                        'blocks' => array('title', 'image', 'title'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'blocks' => array('title', 'image'),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_type_groups'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
     */
    public function testBlockTypeGroupsSettingsWithNoBlockSettings()
    {
        $config = array(
            'block_type_groups' => array(),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
     */
    public function testBlockTypeGroupsSettingsWithNoName()
    {
        $config = array(
            'block_type_groups' => array(
                'block_type_group' => array(),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
     */
    public function testBlockTypeGroupsSettingsWithEmptyName()
    {
        $config = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => '',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
     */
    public function testBlockTypeGroupsSettingsWithInvalidName()
    {
        $config = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
     */
    public function testBlockTypeGroupsSettingsWithEmptyBlockTypeGroups()
    {
        $config = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'blocks' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
     */
    public function testBlockTypeGroupsSettingsWithInvalidBlockTypeGroups()
    {
        $config = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'blocks' => 'paragraph',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
     */
    public function testBlockTypeGroupsSettingsWithEmptyBlockTypeGroupItem()
    {
        $config = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'blocks' => array(''),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
     */
    public function testBlockTypeGroupsSettingsWithInvalidBlockTypeGroupItem()
    {
        $config = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'blocks' => array(array()),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }
}
