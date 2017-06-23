<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class ExtensionPluginTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin
     */
    protected $plugin;

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
        $nodeBuilder = new NodeBuilder();
        $rootNode = $nodeBuilder->arrayNode('test');
        $clonedRootNode = clone $rootNode;

        $this->plugin->addConfiguration($rootNode);

        $this->assertEquals($clonedRootNode, $rootNode);
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
