<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

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
        $this->assertContainerBuilderHasParameter('netgen_block_manager.block_view', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.item_view', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.layout_view', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.form_view', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.pagelayout',
            'NetgenBlockManagerBundle::pagelayout.html.twig'
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
        $this->assertContainerBuilderHasService('netgen_block_manager.block.form.full_edit');
        $this->assertContainerBuilderHasService('netgen_block_manager.normalizer.v1.block');
        $this->assertContainerBuilderHasService('netgen_block_manager.param_converter.page.block');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver');
        $this->assertContainerBuilderHasService('netgen_block_manager.persistence.doctrine.layout_resolver.query_handler.target_handler.route');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver.target_value_provider.route');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver.condition_matcher.route_parameter');
        $this->assertContainerBuilderHasService('netgen_block_manager.view.builder');
        $this->assertContainerBuilderHasService('netgen_block_manager.templating.twig.extension');
        $this->assertContainerBuilderHasService('netgen_block_manager.view.matcher.block.definition');
        $this->assertContainerBuilderHasService('netgen_block_manager.view.provider.block');
        $this->assertContainerBuilderHasService('netgen_block_manager.parameters.form_mapper');
        $this->assertContainerBuilderHasService('netgen_block_manager.collection.result_generator');
        $this->assertContainerBuilderHasService('netgen_block_manager.collection.query.form.full_edit');
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
}
