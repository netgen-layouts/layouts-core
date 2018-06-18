<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\DependencyInjection;

use Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AdminNode;
use Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AppNode;
use Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ExtensionPlugin;
use PHPUnit\Framework\TestCase;

final class ExtensionPluginTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ExtensionPlugin
     */
    private $plugin;

    public function setUp(): void
    {
        $this->plugin = new ExtensionPlugin();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ExtensionPlugin::getConfigurationNodes
     */
    public function testGetConfigurationNodes(): void
    {
        $nodes = $this->plugin->getConfigurationNodes();

        $this->assertCount(2, $nodes);

        $this->assertInstanceOf(AdminNode::class, $nodes[0]);
        $this->assertInstanceOf(AppNode::class, $nodes[1]);
    }
}
