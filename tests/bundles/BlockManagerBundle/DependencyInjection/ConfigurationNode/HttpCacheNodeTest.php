<?php

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
        $config = array(
            array(
                'http_cache' => array(),
            ),
        );

        $expectedConfig = array(
            'http_cache' => array(
                'ttl' => array(
                    'default' => array(
                        'block' => array(),
                    ),
                    'block_definition' => array(),
                ),
            ),
        );

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
        $config = array(
            array(
                'http_cache' => array(
                    'ttl' => array(
                        'default' => array(
                            'block' => array(
                                'shared_max_age' => 42,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'http_cache' => array(
                'ttl' => array(
                    'default' => array(
                        'block' => array(
                            'shared_max_age' => 42,
                        ),
                    ),
                ),
            ),
        );

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
        $config = array(
            array(
                'http_cache' => array(
                    'ttl' => array(
                        'block_definition' => array(
                            'block' => array(
                                'shared_max_age' => 42,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'http_cache' => array(
                'ttl' => array(
                    'block_definition' => array(
                        'block' => array(
                            'shared_max_age' => 42,
                        ),
                    ),
                ),
            ),
        );

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
        $config = array(
            array(
                'http_cache' => array(
                    'invalidation' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'http_cache' => array(
                'invalidation' => array(
                    'enabled' => true,
                ),
            ),
        );

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
        $config = array(
            array(
                'http_cache' => array(
                    'invalidation' => array(
                        'enabled' => false,
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'http_cache' => array(
                'invalidation' => array(
                    'enabled' => false,
                ),
            ),
        );

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
        $config = array(
            'http_cache' => array(
                'ttl' => array(
                    'default' => array(
                        'block' => array(
                            'shared_max_age' => 'invalid',
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::configureTtlNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     */
    public function testBlockDefinitionTtlSettingsWithInvalidSharedMaxAge()
    {
        $config = array(
            'http_cache' => array(
                'ttl' => array(
                    'block_definition' => array(
                        'block' => array(
                            'shared_max_age' => 'invalid',
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     */
    public function testInvalidationSettingsWithInvalidEnabled()
    {
        $config = array(
            'http_cache' => array(
                'invalidation' => array(
                    'enabled' => 42,
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
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
