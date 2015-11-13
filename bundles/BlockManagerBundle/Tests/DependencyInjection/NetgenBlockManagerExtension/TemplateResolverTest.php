<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\NetgenBlockManagerExtension;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class TemplateResolverTest extends AbstractExtensionTestCase
{
    /**
     * Return an array of container extensions that need to be registered for
     * each test (usually just the container extension you are testing).
     *
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return array(
            new NetgenBlockManagerExtension(),
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     */
    public function testDefaultTemplateResolverSettings()
    {
        $config = array(
            'block_view' => array(
                'api' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => array(
                            'block_identifier' => 42,
                        ),
                    ),
                ),
            ),
        );

        $this->load($config);

        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.block_view',
            array(
                'api' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => array(
                            'block_identifier' => 42,
                        ),
                    ),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     */
    public function testDefaultTemplateResolverSettingsWithMatchWithArrayValues()
    {
        $config = array(
            'block_view' => array(
                'api' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => array(24, 42),
                    ),
                ),
            ),
        );

        $this->load($config);

        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.block_view',
            array(
                'api' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => array(24, 42),
                    ),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     */
    public function testDefaultTemplateResolverSettingsWithEmptyMatch()
    {
        $config = array(
            'block_view' => array(
                'api' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => null,
                    ),
                ),
            ),
        );

        $this->load($config);

        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.block_view',
            array(
                'api' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => array(),
                    ),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultTemplateResolverSettingsWithNoMatch()
    {
        $config = array(
            'block_view' => array(
                'api' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                    ),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultTemplateResolverSettingsWithInvalidMatch()
    {
        $config = array(
            'block_view' => array(
                'api' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => 'match',
                    ),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultTemplateResolverSettingsWithNoTemplate()
    {
        $config = array(
            'block_view' => array(
                'api' => array(
                    'block' => array(
                        'match' => array(),
                    ),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultTemplateResolverSettingsWithInvalidTemplate()
    {
        $config = array(
            'block_view' => array(
                'api' => array(
                    'block' => array(
                        'template' => array(),
                        'match' => array(),
                    ),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultTemplateResolverSettingsWithEmptyTemplate()
    {
        $config = array(
            'block_view' => array(
                'api' => array(
                    'block' => array(
                        'template' => '',
                        'match' => array(),
                    ),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultTemplateResolverSettingsWithNoBlocks()
    {
        $config = array(
            'block_view' => array(
                'api' => array(),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultTemplateResolverSettingsWithInvalidBlock()
    {
        $config = array(
            'block_view' => array(
                'api' => array(
                    'block' => 'block',
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultTemplateResolverSettingsWithNoContext()
    {
        $config = array(
            'block_view' => array(),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getTemplateResolverNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultTemplateResolverSettingsWithInvalidContext()
    {
        $config = array(
            'block_view' => array(
                'api' => 'api',
            ),
        );

        $this->load($config);
    }
}
