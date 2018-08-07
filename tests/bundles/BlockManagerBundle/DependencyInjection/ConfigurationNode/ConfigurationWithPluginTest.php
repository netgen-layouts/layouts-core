<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Stubs\ExtensionPlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ConfigurationWithPluginTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testSettings(): void
    {
        $config = [
            [],
        ];

        $expectedConfig = [
            'test_config' => 'test',
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'test_config'
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        $extension = new NetgenBlockManagerExtension();
        $extension->addPlugin(new ExtensionPlugin());

        return new Configuration($extension);
    }
}
