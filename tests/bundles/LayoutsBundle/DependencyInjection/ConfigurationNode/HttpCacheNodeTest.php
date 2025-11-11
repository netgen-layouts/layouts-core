<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\HttpCacheNode;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

#[CoversClass(HttpCacheNode::class)]
#[CoversClass(Configuration::class)]
final class HttpCacheNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testInvalidationSettings(): void
    {
        $config = [
            [
                'http_cache' => [
                    'invalidation' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'http_cache' => [
                'invalidation' => [
                    'enabled' => true,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'http_cache.invalidation',
        );
    }

    public function testDisabledInvalidationSettings(): void
    {
        $config = [
            [
                'http_cache' => [
                    'invalidation' => [
                        'enabled' => false,
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'http_cache' => [
                'invalidation' => [
                    'enabled' => false,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'http_cache.invalidation.enabled',
        );
    }

    public function testInvalidationSettingsWithInvalidEnabled(): void
    {
        $config = [
            'http_cache' => [
                'invalidation' => [
                    'enabled' => 42,
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenLayoutsExtension());
    }
}
