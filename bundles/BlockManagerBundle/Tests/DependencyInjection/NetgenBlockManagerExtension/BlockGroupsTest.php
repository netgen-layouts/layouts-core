<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\NetgenBlockManagerExtension;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class BlockGroupsTest extends AbstractExtensionTestCase
{
    /**
     * Return an array of container extensions that need to be registered for
     * each test (usually just the container extension you are testing).
     *
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return array(
            new NetgenBlockManagerExtension(),
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     */
    public function testDefaultBlockGroupsSettings()
    {
        $config = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                ),
            ),
        );

        $this->load($config);

        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.block_groups',
            array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array(),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     */
    public function testDefaultBlockGroupsSettingsWithBlocks()
    {
        $config = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array('title', 'image'),
                ),
            ),
        );

        $this->load($config);

        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.block_groups',
            array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array('title', 'image'),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     */
    public function testDefaultBlockGroupsSettingsWithNonUniqueBlocks()
    {
        $config = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array('title', 'image', 'title'),
                ),
            ),
        );

        $this->load($config);

        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.block_groups',
            array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array('title', 'image'),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultBlockGroupsSettingsWithNoBlockSettings()
    {
        $config = array('block_groups' => array());
        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultBlockGroupsSettingsWithNoName()
    {
        $config = array('block_groups' => array('block_group' => array()));
        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultBlockGroupsSettingsWithEmptyName()
    {
        $config = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => '',
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultBlockGroupsSettingsWithInvalidName()
    {
        $config = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => array(),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultBlockGroupsSettingsWithEmptyBlockGroups()
    {
        $config = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array(),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultBlockGroupsSettingsWithInvalidBlockGroups()
    {
        $config = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => 'paragraph',
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultBlockGroupsSettingsWithEmptyBlockGroupItem()
    {
        $config = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array(''),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultBlockGroupsSettingsWithInvalidBlockGroupItem()
    {
        $config = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array(array()),
                ),
            ),
        );

        $this->load($config);
    }
}
