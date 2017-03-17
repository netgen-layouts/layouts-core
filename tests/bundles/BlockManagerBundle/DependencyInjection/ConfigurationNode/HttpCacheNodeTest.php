<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

class HttpCacheNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
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
                        'block' => array(
                            'max_age' => 0,
                            'shared_max_age' => 300,
                            'overwrite_headers' => false,
                        ),
                        'layout' => array(
                            'max_age' => 0,
                            'shared_max_age' => 300,
                            'overwrite_headers' => false,
                        ),
                    ),
                    'layout_type' => array(),
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockTtlSettings()
    {
        $config = array(
            array(
                'http_cache' => array(
                    'ttl' => array(
                        'default' => array(
                            'block' => array(
                                'max_age' => 24,
                                'shared_max_age' => 42,
                                'overwrite_headers' => true,
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
                            'max_age' => 24,
                            'shared_max_age' => 42,
                            'overwrite_headers' => true,
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionTtlSettings()
    {
        $config = array(
            array(
                'http_cache' => array(
                    'ttl' => array(
                        'block_definition' => array(
                            'block' => array(
                                'max_age' => 24,
                                'shared_max_age' => 42,
                                'overwrite_headers' => true,
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
                            'max_age' => 24,
                            'shared_max_age' => 42,
                            'overwrite_headers' => true,
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testLayoutTtlSettings()
    {
        $config = array(
            array(
                'http_cache' => array(
                    'ttl' => array(
                        'default' => array(
                            'layout' => array(
                                'max_age' => 24,
                                'shared_max_age' => 42,
                                'overwrite_headers' => true,
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
                        'layout' => array(
                            'max_age' => 24,
                            'shared_max_age' => 42,
                            'overwrite_headers' => true,
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'http_cache.ttl.default.layout'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testLayoutTypeTtlSettings()
    {
        $config = array(
            array(
                'http_cache' => array(
                    'ttl' => array(
                        'layout_type' => array(
                            'layout' => array(
                                'max_age' => 24,
                                'shared_max_age' => 42,
                                'overwrite_headers' => true,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'http_cache' => array(
                'ttl' => array(
                    'layout_type' => array(
                        'layout' => array(
                            'max_age' => 24,
                            'shared_max_age' => 42,
                            'overwrite_headers' => true,
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'http_cache.ttl.layout_type.*'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testInvalidationSettings()
    {
        $config = array(
            array(
                'http_cache' => array(
                    'invalidation' => array(
                        'default_strategy' => 'bla',
                        'strategies' => array(
                            'ban' => array(
                                'block' => array(
                                    'invalidator' => 'ban.block.invalidator',
                                    'tagger' => 'ban.block.tagger',
                                ),
                                'layout' => array(
                                    'invalidator' => 'ban.layout.invalidator',
                                    'tagger' => 'ban.layout.tagger',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'http_cache' => array(
                'invalidation' => array(
                    'enabled' => true,
                    'default_strategy' => 'bla',
                    'strategies' => array(
                        'ban' => array(
                            'block' => array(
                                'invalidator' => 'ban.block.invalidator',
                                'tagger' => 'ban.block.tagger',
                            ),
                            'layout' => array(
                                'invalidator' => 'ban.layout.invalidator',
                                'tagger' => 'ban.layout.tagger',
                            ),
                        ),
                    ),
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
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

    // @TODO Test invalid settings

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
