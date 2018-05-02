<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Stubs\ExtensionPlugin;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\NetgenContentBrowserExtension;

final class NetgenBlockManagerExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @var array
     */
    private $minimalConfig = [
        'design_list' => [
            'standard' => [],
        ],
    ];

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension
     */
    private $extension;

    public function setUp()
    {
        parent::setUp();

        /** @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension $extension */
        $extension = $this->container->getExtension('netgen_block_manager');

        $this->extension = $extension;
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::addPlugin
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::hasPlugin
     */
    public function testHasPlugin()
    {
        $this->assertTrue($this->extension->hasPlugin(ExtensionPlugin::class));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::addPlugin
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::getPlugin
     */
    public function testGetPlugin()
    {
        $this->assertInstanceOf(ExtensionPlugin::class, $this->extension->getPlugin(ExtensionPlugin::class));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::addPlugin
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::getPlugin
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Extension plugin "unknown" does not exist
     */
    public function testGetPluginThrowsRuntimeException()
    {
        $this->extension->getPlugin('unknown');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::addPlugin
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::getPlugins
     */
    public function testGetPlugins()
    {
        $plugins = $this->extension->getPlugins();

        $this->assertInternalType('array', $plugins);
        $this->assertArrayHasKey(ExtensionPlugin::class, $plugins);
        $this->assertCount(1, $plugins);
        $this->assertInstanceOf(ExtensionPlugin::class, $plugins[ExtensionPlugin::class]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::loadConfigFiles
     */
    public function testParameters()
    {
        $this->load($this->minimalConfig);

        $this->assertContainerBuilderHasParameter('netgen_block_manager.route_prefix', '/bm');
        $this->assertContainerBuilderHasParameter('netgen_block_manager.block_definitions', []);
        $this->assertContainerBuilderHasParameter('netgen_block_manager.block_types', []);
        $this->assertContainerBuilderHasParameter('netgen_block_manager.block_type_groups', []);
        $this->assertContainerBuilderHasParameter('netgen_block_manager.layout_types', []);
        $this->assertContainerBuilderHasParameter('netgen_block_manager.query_types', []);
        $this->assertContainerBuilderHasParameter('netgen_block_manager.view', []);
        $this->assertContainerBuilderHasParameter('netgen_block_manager.http_cache');
        $this->assertContainerBuilderHasParameter('netgen_block_manager.design_list', ['standard' => []]);
        $this->assertContainerBuilderHasParameter('netgen_block_manager.design', 'standard');
        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.pagelayout',
            '@NetgenBlockManager/empty_pagelayout.html.twig'
        );
    }

    /**
     * We test for existence of one service from each of the config files.
     *
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::loadConfigFiles
     */
    public function testServices()
    {
        $this->load($this->minimalConfig);

        $this->assertContainerBuilderHasService('netgen_block_manager.block.registry.block_definition');
        $this->assertContainerBuilderHasService('netgen_block_manager.controller.base');
        $this->assertContainerBuilderHasService('netgen_block_manager.event_listener.exception_conversion');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.form.create');
        $this->assertContainerBuilderHasService('netgen_block_manager.normalizer.v1.block');
        $this->assertContainerBuilderHasService('netgen_block_manager.param_converter.block.block');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.registry.layout_type');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver.target_handler.doctrine.route');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver.target_type.route');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver.condition_type.route_parameter');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver.form.condition_type');
        $this->assertContainerBuilderHasService('netgen_block_manager.view.view_builder');
        $this->assertContainerBuilderHasService('netgen_block_manager.templating.twig.extension.rendering');
        $this->assertContainerBuilderHasService('netgen_block_manager.view.matcher.block.definition');
        $this->assertContainerBuilderHasService('netgen_block_manager.view.provider.block');
        $this->assertContainerBuilderHasService('netgen_block_manager.parameters.registry.parameter_type');
        $this->assertContainerBuilderHasService('netgen_block_manager.collection.result_builder');
        $this->assertContainerBuilderHasService('netgen_block_manager.item.item_builder');
        $this->assertContainerBuilderHasService('netgen_block_manager.http_cache.client');
        $this->assertContainerBuilderHasService('netgen_block_manager.context');

        $this->assertContainerBuilderHasService('netgen_block_manager.core.service.block');
        $this->assertContainerBuilderHasAlias(
            'netgen_block_manager.api.service.block',
            'netgen_block_manager.core.service.block'
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.configuration.container');
        $this->assertContainerBuilderHasAlias(
            'netgen_block_manager.configuration',
            'netgen_block_manager.configuration.container'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::getConfiguration
     */
    public function testGetConfiguration()
    {
        $configuration = $this->extension->getConfiguration([], $this->container);
        $this->assertInstanceOf(Configuration::class, $configuration);
    }

    /**
     * We test for existence of one config value from each of the config files.
     *
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::prepend
     */
    public function testPrepend()
    {
        $this->container->setParameter('kernel.bundles', ['NetgenContentBrowserBundle' => true]);
        $this->container->registerExtension(new NetgenContentBrowserExtension());

        $this->extension->prepend($this->container);

        $config = array_merge_recursive(
            ...$this->container->getExtensionConfig('netgen_block_manager')
        );

        $this->assertInternalType('array', $config);

        $this->assertArrayHasKey('design_list', $config);
        $this->assertArrayHasKey('standard', $config['design_list']);
        $this->assertNull($config['design_list']['standard']);

        $this->assertArrayHasKey('view', $config);

        $this->assertArrayHasKey('parameter_view', $config['view']);

        $this->assertArrayHasKey('default_view_templates', $config);
        $this->assertArrayHasKey('block_view', $config['default_view_templates']);

        $browserConfig = array_merge_recursive(
            ...$this->container->getExtensionConfig('netgen_content_browser')
        );

        $this->assertInternalType('array', $browserConfig);

        $this->assertArrayHasKey('item_types', $browserConfig);
        $this->assertArrayHasKey('ngbm_layout', $browserConfig['item_types']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::prepend
     */
    public function testAppendFromPlugin()
    {
        $this->extension->prepend($this->container);

        $config = array_merge_recursive(
            ...$this->container->getExtensionConfig('netgen_block_manager')
        );

        $this->assertInternalType('array', $config);

        $this->assertArrayHasKey('block_types', $config);
        $this->assertArrayHasKey('test_type', $config['block_types']);

        $this->assertEquals(
            [
                'name' => 'Test type',
                'definition_identifier' => 'title',
            ],
            $config['block_types']['test_type']
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::processHttpCacheConfiguration
     */
    public function testHttpCacheConfiguration()
    {
        $this->load($this->minimalConfig);

        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.http_cache.ttl.default.block',
            []
        );

        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.http_cache.ttl.block_definition',
            []
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::validateCurrentDesign
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Design "non_existing" does not exist. Available designs are: standard
     */
    public function testNonExistingCurrentDesign()
    {
        $this->load($this->minimalConfig + ['design' => 'non_existing']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::validateCurrentDesign
     */
    public function testStandardAsCurrentDesign()
    {
        $this->load($this->minimalConfig + ['design' => 'standard']);

        // Avoids a warning in test runner about tests which do not assert anything
        $this->assertTrue(true);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::validateCurrentDesign
     */
    public function testCustomDesignAsCurrentDesign()
    {
        $designList = $this->minimalConfig;
        $designList['design_list']['custom'] = [];

        $this->load($designList + ['design' => 'custom']);

        // Avoids a warning in test runner about tests which do not assert anything
        $this->assertTrue(true);
    }

    /**
     * Return an array of container extensions that need to be registered for
     * each test (usually just the container extension you are testing).
     *
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        $extension = new NetgenBlockManagerExtension();
        $extension->addPlugin(new ExtensionPlugin());

        return [$extension];
    }
}
