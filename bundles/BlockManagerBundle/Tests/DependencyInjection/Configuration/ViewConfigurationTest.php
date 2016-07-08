<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Configuration;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class ViewConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        $extension = new NetgenBlockManagerExtension();

        return new Configuration($extension->getAlias());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getViewNodeDefinition
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getViewNodeDefinition
     */
    public function testViewSettingsWithMatchWithArrayValues()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'some_context' => array(
                            'block' => array(
                                'template' => 'block.html.twig',
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
                            'template' => 'block.html.twig',
                            'match' => array(24, 42),
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getViewNodeDefinition
     */
    public function testViewSettingsWithEmptyMatch()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'some_context' => array(
                            'block' => array(
                                'template' => 'block.html.twig',
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
                            'template' => 'block.html.twig',
                            'match' => array(),
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getViewNodeDefinition
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getViewNodeDefinition
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getViewNodeDefinition
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getViewNodeDefinition
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getViewNodeDefinition
     */
    public function testViewSettingsWithNoViews()
    {
        $config = array(
            'view' => array(),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }
}
