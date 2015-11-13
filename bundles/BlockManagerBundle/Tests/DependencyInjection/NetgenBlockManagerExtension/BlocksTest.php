<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\NetgenBlockManagerExtension;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class BlocksTest extends AbstractExtensionTestCase
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testDefaultBlockSettings()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'name' => 'block',
                ),
            ),
        );

        $this->load($config);

        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.blocks',
            array(
                'block' => array(
                    'name' => 'block',
                    'view_types' => array('default'),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testDefaultBlockSettingsWithViewTypes()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'name' => 'block',
                    'view_types' => array('small', 'large'),
                ),
            ),
        );

        $this->load($config);

        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.blocks',
            array(
                'block' => array(
                    'name' => 'block',
                    'view_types' => array('small', 'large'),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testDefaultBlockSettingsWithNonUniqueViewTypes()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'name' => 'block',
                    'view_types' => array('small', 'large', 'small'),
                ),
            ),
        );

        $this->load($config);

        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.blocks',
            array(
                'block' => array(
                    'name' => 'block',
                    'view_types' => array('small', 'large'),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultBlockSettingsWithNoBlocks()
    {
        $config = array('blocks' => array());
        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultBlockSettingsWithNoName()
    {
        $config = array('blocks' => array('block' => array()));
        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultBlockSettingsWithEmptyName()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'name' => '',
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultBlockSettingsWithInvalidName()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'name' => array(),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultBlockSettingsWithEmptyViewTypes()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'name' => 'block',
                    'view_types' => array(),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultBlockSettingsWithInvalidViewTypes()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'name' => 'block',
                    'view_types' => 'default',
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultBlockSettingsWithEmptyViewTypeItem()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'name' => 'block',
                    'view_types' => array(''),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultBlockSettingsWithInvalidViewTypeItem()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'name' => 'block',
                    'view_types' => array(array()),
                ),
            ),
        );

        $this->load($config);
    }
}
