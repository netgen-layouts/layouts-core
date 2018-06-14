<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class HttpCacheNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::configureTtlNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     */
    public function testDefaultTtlSettings(): void
    {
        $config = [
            [
                'http_cache' => [],
            ],
        ];

        $expectedConfig = [
            'http_cache' => [
                'ttl' => [
                    'default' => [
                        'block' => [],
                    ],
                    'block_definition' => [],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'http_cache.ttl'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::configureTtlNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     */
    public function testBlockTtlSettings(): void
    {
        $config = [
            [
                'http_cache' => [
                    'ttl' => [
                        'default' => [
                            'block' => [
                                'shared_max_age' => 42,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'http_cache' => [
                'ttl' => [
                    'default' => [
                        'block' => [
                            'shared_max_age' => 42,
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'http_cache.ttl.default.block'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::configureTtlNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     */
    public function testBlockDefinitionTtlSettings(): void
    {
        $config = [
            [
                'http_cache' => [
                    'ttl' => [
                        'block_definition' => [
                            'block' => [
                                'shared_max_age' => 42,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'http_cache' => [
                'ttl' => [
                    'block_definition' => [
                        'block' => [
                            'shared_max_age' => 42,
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'http_cache.ttl.block_definition.*'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     */
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
            'http_cache.invalidation'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     */
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
            'http_cache.invalidation.enabled'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::configureTtlNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     */
    public function testDefaultBlockTtlSettingsWithInvalidSharedMaxAge(): void
    {
        $config = [
            'http_cache' => [
                'ttl' => [
                    'default' => [
                        'block' => [
                            'shared_max_age' => 'invalid',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::configureTtlNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     */
    public function testBlockDefinitionTtlSettingsWithInvalidSharedMaxAge(): void
    {
        $config = [
            'http_cache' => [
                'ttl' => [
                    'block_definition' => [
                        'block' => [
                            'shared_max_age' => 'invalid',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     */
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
        return new Configuration(new NetgenBlockManagerExtension());
    }
}
