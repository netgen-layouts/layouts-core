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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::configureTtlNode
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::configureTtlNode
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::configureTtlNode
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

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::configureTtlNode
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::configureTtlNode
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
    public function testInvalidationSettingsWithInvalidDefaultStrategy()
    {
        $config = array(
            'http_cache' => array(
                'invalidation' => array(
                    'default_strategy' => array(),
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     */
    public function testInvalidationSettingsWithEmptyStrategies()
    {
        $config = array(
            'http_cache' => array(
                'invalidation' => array(
                    'strategies' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\HttpCacheNode::getConfigurationNode
     */
    public function testInvalidationSettingsWithMissingLayoutTagger()
    {
        $config = array(
            'http_cache' => array(
                'invalidation' => array(
                    'strategies' => array(
                        'ban' => array(
                            'layout' => array(
                                'invalidator' => 'test',
                            ),
                            'block' => array(
                                'tagger' => 'test',
                                'invalidator' => 'test',
                            ),
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
    public function testInvalidationSettingsWithMissingLayoutInvalidator()
    {
        $config = array(
            'http_cache' => array(
                'invalidation' => array(
                    'strategies' => array(
                        'ban' => array(
                            'layout' => array(
                                'tagger' => 'test',
                            ),
                            'block' => array(
                                'tagger' => 'test',
                                'invalidator' => 'test',
                            ),
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
    public function testInvalidationSettingsWithMissingBlockTagger()
    {
        $config = array(
            'http_cache' => array(
                'invalidation' => array(
                    'strategies' => array(
                        'ban' => array(
                            'layout' => array(
                                'tagger' => 'test',
                                'invalidator' => 'test',
                            ),
                            'block' => array(
                                'invalidator' => 'test',
                            ),
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
    public function testInvalidationSettingsWithMissingBlockInvalidator()
    {
        $config = array(
            'http_cache' => array(
                'invalidation' => array(
                    'strategies' => array(
                        'ban' => array(
                            'layout' => array(
                                'tagger' => 'test',
                                'invalidator' => 'test',
                            ),
                            'block' => array(
                                'invalidator' => 'test',
                            ),
                        ),
                    ),
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
