<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\DebugNode;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

#[CoversClass(DebugNode::class)]
#[CoversClass(Configuration::class)]
final class DebugNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testDebugSettings(): void
    {
        $config = [
            [
                'debug' => true,
            ],
        ];

        $expectedConfig = [
            'debug' => true,
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'debug',
        );
    }

    public function testDefaultDebugSettings(): void
    {
        $config = [
            [],
        ];

        $expectedConfig = [
            'debug' => false,
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'debug',
        );
    }

    public function testDebugSettingsWithInvalidDebugConfig(): void
    {
        $config = [
            [
                'debug' => [],
            ],
        ];

        $this->assertConfigurationIsInvalid(
            $config,
            '/Invalid type for path "netgen_layouts.debug". Expected "?bool(ean)?"?, but got "?array"?./',
            true,
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenLayoutsExtension());
    }
}
