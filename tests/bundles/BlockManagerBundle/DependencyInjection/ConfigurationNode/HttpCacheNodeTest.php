<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

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
    public function testDefaultTtlSettings()
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
    public function testBlockTtlSettings()
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
    public function testBlockDefinitionTtlSettings()
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
    public function testInvalidationSettings()
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
    public function testDisabledInvalidationSettings()
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
    public function testDefaultBlockTtlSettingsWithInvalidSharedMaxAge()
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
    public function testBlockDefinitionTtlSettingsWithInvalidSharedMaxAge()
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
    public function testInvalidationSettingsWithInvalidEnabled()
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

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}
