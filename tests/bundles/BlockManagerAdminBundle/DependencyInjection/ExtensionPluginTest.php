<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\DependencyInjection;

use Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode;
use Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ExtensionPlugin;
use PHPUnit\Framework\TestCase;

final class ExtensionPluginTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ExtensionPlugin
     */
    private $plugin;

    public function setUp()
    {
        $this->plugin = new ExtensionPlugin();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ExtensionPlugin::getConfigurationNodes
     */
    public function testGetConfigurationNodes()
    {
        $nodes = $this->plugin->getConfigurationNodes();

        $this->assertEquals(
            array(
                new ConfigurationNode\AdminNode(),
                new ConfigurationNode\AppNode(),
            ),
            $nodes
        );
    }
}
