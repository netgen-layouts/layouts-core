<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Configuration;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;

class TemplateResolverConfigurationTest extends \PHPUnit_Framework_TestCase
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     */
    public function testTemplateResolverSettings()
    {
        $config = array(
            array(
                'block_view' => array(
                    'view' => array(
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

        $expectedConfig = array(
            'block_view' => array(
                'view' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => array(
                            'block_identifier' => 42,
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_view'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     */
    public function testTemplateResolverSettingsWithMatchWithArrayValues()
    {
        $config = array(
            array(
                'block_view' => array(
                    'view' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => array(24, 42),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_view' => array(
                'view' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => array(24, 42),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_view'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     */
    public function testTemplateResolverSettingsWithEmptyMatch()
    {
        $config = array(
            array(
                'block_view' => array(
                    'view' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => null,
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_view' => array(
                'view' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => array(),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_view'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     */
    public function testTemplateResolverSettingsWithNoMatch()
    {
        $config = array(
            'block_view' => array(
                'view' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     */
    public function testTemplateResolverSettingsWithInvalidMatch()
    {
        $config = array(
            'block_view' => array(
                'view' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => 'match',
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     */
    public function testTemplateResolverSettingsWithNoTemplate()
    {
        $config = array(
            'block_view' => array(
                'view' => array(
                    'block' => array(
                        'match' => array(),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     */
    public function testTemplateResolverSettingsWithInvalidTemplate()
    {
        $config = array(
            'block_view' => array(
                'view' => array(
                    'block' => array(
                        'template' => array(),
                        'match' => array(),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     */
    public function testTemplateResolverSettingsWithEmptyTemplate()
    {
        $config = array(
            'block_view' => array(
                'view' => array(
                    'block' => array(
                        'template' => '',
                        'match' => array(),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     */
    public function testTemplateResolverSettingsWithNoBlocks()
    {
        $config = array(
            'block_view' => array(
                'view' => array(),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     */
    public function testTemplateResolverSettingsWithInvalidBlock()
    {
        $config = array(
            'block_view' => array(
                'view' => array(
                    'block' => 'block',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     */
    public function testTemplateResolverSettingsWithNoContext()
    {
        $config = array(
            'block_view' => array(),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     */
    public function testTemplateResolverSettingsWithInvalidContext()
    {
        $config = array(
            'block_view' => array(
                'view' => 'view',
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }
}
