<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\Stubs\ExtensionPlugin;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

#[CoversClass(Configuration::class)]
final class ConfigurationWithPluginTest extends TestCase
{
    use ConfigurationTestCaseTrait;

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
            'test_config',
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        $extension = new NetgenLayoutsExtension();
        $extension->addPlugin(new ExtensionPlugin());

        return new Configuration($extension);
    }
}
