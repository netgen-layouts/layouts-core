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

        $this->assertContainerBuilderHasParameter('netgen_block_manager.blocks', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.block_groups', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.layouts', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.block_view', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.layout_view', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.pagelayout',
            'NetgenBlockManagerBundle::pagelayout_empty.html.twig'
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

        $this->assertContainerBuilderHasService('netgen_block_manager.block_definition.registry');
        $this->assertContainerBuilderHasService('netgen_block_manager.controller.base');
        $this->assertContainerBuilderHasService('netgen_block_manager.event_listener.exception_conversion');
        $this->assertContainerBuilderHasService('netgen_block_manager.form.update_block');
        $this->assertContainerBuilderHasService('netgen_block_manager.normalizer.block');
        $this->assertContainerBuilderHasService('netgen_block_manager.param_converter.block');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout_resolver');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout_resolver.target_builder.route');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout_resolver.rule_handler.doctrine.target_handler.route');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout_resolver.condition_matcher.route_parameter');
        $this->assertContainerBuilderHasService('netgen_block_manager.view.builder');
        $this->assertContainerBuilderHasService('netgen_block_manager.templating.twig.extension');
        $this->assertContainerBuilderHasService('netgen_block_manager.view.matcher.block.definition_identifier');
        $this->assertContainerBuilderHasService('netgen_block_manager.view.provider.block');
        $this->assertContainerBuilderHasService('netgen_block_manager.view.template_resolver.block_view');

        $this->assertContainerBuilderHasService('netgen_block_manager.api.service.block.core');
        $this->assertContainerBuilderHasAlias(
            'netgen_block_manager.api.service.block',
            'netgen_block_manager.api.service.block.core'
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.configuration.container');
        $this->assertContainerBuilderHasAlias(
            'netgen_block_manager.configuration',
            'netgen_block_manager.configuration.container'
        );
    }
}
