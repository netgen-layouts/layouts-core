<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\NetgenContentBrowserExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NetgenBlockManagerExtensionTest extends AbstractExtensionTestCase
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::loadConfigFiles
     */
    public function testParameters()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('netgen_block_manager.route_prefix', '/bm');
        $this->assertContainerBuilderHasParameter('netgen_block_manager.block_definitions', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.block_types', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.block_type_groups', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.layout_types', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.sources', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.query_types', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.view', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.pagelayout',
            'NetgenBlockManagerBundle::empty_pagelayout.html.twig'
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
        $this->load();

        $this->assertContainerBuilderHasService('netgen_block_manager.block.registry.block_definition');
        $this->assertContainerBuilderHasService('netgen_block_manager.controller.base');
        $this->assertContainerBuilderHasService('netgen_block_manager.event_listener.exception_conversion');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.form.create');
        $this->assertContainerBuilderHasService('netgen_block_manager.normalizer.v1.block');
        $this->assertContainerBuilderHasService('netgen_block_manager.param_converter.page.block');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver');
        $this->assertContainerBuilderHasService('netgen_block_manager.persistence.doctrine.layout_resolver.query_handler.target_handler.route');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver.target_type.route');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver.condition_type.route_parameter');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver.form.condition_type');
        $this->assertContainerBuilderHasService('netgen_block_manager.view.builder');
        $this->assertContainerBuilderHasService('netgen_block_manager.templating.twig.extension.rendering');
        $this->assertContainerBuilderHasService('netgen_block_manager.view.matcher.block.definition');
        $this->assertContainerBuilderHasService('netgen_block_manager.view.provider.block');
        $this->assertContainerBuilderHasService('netgen_block_manager.parameters.registry.parameter_type');
        $this->assertContainerBuilderHasService('netgen_block_manager.collection.result_loader');
        $this->assertContainerBuilderHasService('netgen_block_manager.item.item_builder');

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
        $container = new ContainerBuilder();
        $extension = new NetgenBlockManagerExtension();

        $configuration = $extension->getConfiguration(array(), $container);
        $this->assertInstanceOf(Configuration::class, $configuration);
    }

    /**
     * We test for existence of one config value from each of the config files.
     *
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::prepend
     */
    public function testPrepend()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.bundles', array('NetgenContentBrowserBundle' => true));
        $container->registerExtension(new NetgenContentBrowserExtension());
        $extension = new NetgenBlockManagerExtension();

        $extension->prepend($container);

        $config = call_user_func_array(
            'array_merge_recursive',
            $container->getExtensionConfig('netgen_block_manager')
        );

        $this->assertInternalType('array', $config);

        $this->assertArrayHasKey('block_definitions', $config);
        $this->assertArrayHasKey('title', $config['block_definitions']);

        $this->assertArrayHasKey('block_type_groups', $config);
        $this->assertArrayHasKey('basic', $config['block_type_groups']);

        $this->assertArrayHasKey('block_types', $config);
        $this->assertArrayHasKey('grid', $config['block_types']);

        $this->assertArrayHasKey('layout_types', $config);
        $this->assertArrayHasKey('layout_1', $config['layout_types']);

        $this->assertArrayHasKey('view', $config);

        $this->assertArrayHasKey('block_view', $config['view']);
        $this->assertArrayHasKey('layout_view', $config['view']);
        $this->assertArrayHasKey('parameter_view', $config['view']);

        $this->assertArrayHasKey('default_view_templates', $config);
        $this->assertArrayHasKey('block_view', $config['default_view_templates']);

        $browserConfig = call_user_func_array(
            'array_merge_recursive',
            $container->getExtensionConfig('netgen_content_browser')
        );

        $this->assertInternalType('array', $browserConfig);

        $this->assertArrayHasKey('item_types', $browserConfig);
        $this->assertArrayHasKey('ngbm_layout', $browserConfig['item_types']);
    }
}
