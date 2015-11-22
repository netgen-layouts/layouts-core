<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Configuration;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;

class BlockGroupsConfigurationTest extends \PHPUnit_Framework_TestCase
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     */
    public function testBlockGroupsSettings()
    {
        $config = array(
            array(
                'block_groups' => array(
                    'block_group' => array(
                        'name' => 'block_group',
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array(),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_groups'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     */
    public function testBlockGroupsSettingsWithBlocksMerge()
    {
        $config = array(
            array(
                'block_groups' => array(
                    'block_group' => array(
                        'name' => 'block_group',
                        'blocks' => array('title', 'paragraph'),
                    ),
                ),
            ),
            array(
                'block_groups' => array(
                    'block_group' => array(
                        'name' => 'block_group',
                        'blocks' => array('image'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array('title', 'paragraph', 'image'),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_groups'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     */
    public function testBlockGroupsSettingsWithBlocks()
    {
        $config = array(
            array(
                'block_groups' => array(
                    'block_group' => array(
                        'name' => 'block_group',
                        'blocks' => array('title', 'image'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array('title', 'image'),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_groups'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     */
    public function testBlockGroupsSettingsWithNonUniqueBlocks()
    {
        $config = array(
            array(
                'block_groups' => array(
                    'block_group' => array(
                        'name' => 'block_group',
                        'blocks' => array('title', 'image', 'title'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array('title', 'image'),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_groups'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     */
    public function testBlockGroupsSettingsWithNoBlockSettings()
    {
        $config = array(
            'block_groups' => array(),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     */
    public function testBlockGroupsSettingsWithNoName()
    {
        $config = array(
            'block_groups' => array(
                'block_group' => array(),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     */
    public function testBlockGroupsSettingsWithEmptyName()
    {
        $config = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => '',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     */
    public function testBlockGroupsSettingsWithInvalidName()
    {
        $config = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     */
    public function testBlockGroupsSettingsWithEmptyBlockGroups()
    {
        $config = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     */
    public function testBlockGroupsSettingsWithInvalidBlockGroups()
    {
        $config = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => 'paragraph',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     */
    public function testBlockGroupsSettingsWithEmptyBlockGroupItem()
    {
        $config = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array(''),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     */
    public function testBlockGroupsSettingsWithInvalidBlockGroupItem()
    {
        $config = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array(array()),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }
}
