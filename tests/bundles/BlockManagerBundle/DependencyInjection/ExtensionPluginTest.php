<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use PHPUnit\Framework\TestCase;

class ExtensionPluginText extends TestCase
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
        $processedConfig = $this->plugin->preProcessConfiguration(array(), new ContainerBuilder());
        $this->assertEquals(array(), $processedConfig);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     */
    public function testPostProcessConfiguration()
    {
        $processedConfig = $this->plugin->postProcessConfiguration(array(), new ContainerBuilder());
        $this->assertEquals(array(), $processedConfig);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin::appendConfigurationFiles
     */
    public function testAppendConfigurationFiles()
    {
        $this->assertEquals(array(), $this->plugin->appendConfigurationFiles());
    }
}
