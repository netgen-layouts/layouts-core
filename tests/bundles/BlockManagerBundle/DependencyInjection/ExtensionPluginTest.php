<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Stubs\ConfigurationNode;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class ExtensionPluginTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin
     */
    private $plugin;

    public function setUp()
    {
        $this->plugin = $this->getMockForAbstractClass(ExtensionPlugin::class);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     */
    public function testPreProcessConfiguration()
    {
        $processedConfig = $this->plugin->preProcessConfiguration(array());
        $this->assertEquals(array(), $processedConfig);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     */
    public function testPostProcessConfiguration()
    {
        $processedConfig = $this->plugin->postProcessConfiguration(array());
        $this->assertEquals(array(), $processedConfig);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     */
    public function testAddConfiguration()
    {
        $node1 = new ConfigurationNode();
        $node2 = new ConfigurationNode();

        $this->plugin = $this->getMockForAbstractClass(
            ExtensionPlugin::class,
            array(),
            '',
            true,
            true,
            true,
            array('getConfigurationNodes')
        );

        $this->plugin
            ->expects($this->once())
            ->method('getConfigurationNodes')
            ->will($this->returnValue(array($node1, $node2)));

        $rootNodeMock = $this->createMock(ArrayNodeDefinition::class);
        $nodeBuilderMock = $this->createMock(NodeBuilder::class);

        $rootNodeMock
            ->expects($this->at(0))
            ->method('children')
            ->will($this->returnValue($nodeBuilderMock));

        $nodeBuilderMock
            ->expects($this->at(0))
            ->method('append')
            ->with($this->equalTo($node1->getConfigurationNode()));

        $nodeBuilderMock
            ->expects($this->at(1))
            ->method('append')
            ->with($this->equalTo($node2->getConfigurationNode()));

        $this->plugin->addConfiguration($rootNodeMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin::getConfigurationNodes
     */
    public function testGetConfigurationNodes()
    {
        $this->assertEquals(array(), $this->plugin->getConfigurationNodes());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin::appendConfigurationFiles
     */
    public function testAppendConfigurationFiles()
    {
        $this->assertEquals(array(), $this->plugin->appendConfigurationFiles());
    }
}
