<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

class ViewNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettings()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'some_context' => array(
                            'block' => array(
                                'template' => 'block.html.twig',
                                'match' => array(
                                    'block_identifier' => 42,
                                ),
                                'parameters' => array(
                                    'param' => 'value',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => array(
                                'block_identifier' => 42,
                            ),
                            'parameters' => array(
                                'param' => 'value',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'view'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettingsWithMatchWithArrayValues()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'some_context' => array(
                            'block' => array(
                                'match' => array(24, 42),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(
                        'block' => array(
                            'match' => array(24, 42),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'view.*.*.*.match'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettingsWithEmptyMatch()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'some_context' => array(
                            'block' => array(
                                'match' => null,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(
                        'block' => array(
                            'match' => array(),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'view.*.*.*.match'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettingsWithNoParameters()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'some_context' => array(
                            'block' => array(),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(
                        'block' => array(
                            'parameters' => array(),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'view.*.*.*.parameters'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettingsWithNoMatch()
    {
        $config = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettingsWithNoTemplate()
    {
        $config = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(
                        'block' => array(
                            'match' => array(),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettingsWithNoBlocks()
    {
        $config = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettingsWithNoContext()
    {
        $config = array(
            'view' => array(
                'block_view' => array(),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode::getConfigurationNode
     */
    public function testViewSettingsWithNoViews()
    {
        $config = array(
            'view' => array(),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    private function getConfiguration()
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}
