<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\NetgenBlockManagerAdminExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;

final class NetgenBlockManagerAdminExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\NetgenBlockManagerAdminExtension::load
     */
    public function testParameters()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('netgen_block_manager.admin.csrf_token_id', 'ngbm_admin');
        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.admin.pagelayout',
            '@NetgenBlockManagerAdmin/admin/pagelayout.html.twig'
        );
    }

    /**
     * We test for existence of one service from each of the config files.
     *
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\NetgenBlockManagerAdminExtension::load
     */
    public function testServices()
    {
        $this->load();

        $this->assertContainerBuilderHasService('netgen_block_manager.menu.admin.main_menu');
        $this->assertContainerBuilderHasService('netgen_block_manager.templating.admin_global_variable');
        $this->assertContainerBuilderHasService('netgen_block_manager.event_listener.set_is_admin_request');
        $this->assertContainerBuilderHasService('netgen_block_manager.controller.admin.layouts.index');
    }

    /**
     * We test for existence of one config value from each of the config files.
     *
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\NetgenBlockManagerAdminExtension::prepend
     */
    public function testPrepend()
    {
        $this->container->setParameter('kernel.bundles', ['NetgenBlockManagerBundle' => true]);
        $this->container->registerExtension(new NetgenBlockManagerExtension());

        /** @var \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\NetgenBlockManagerAdminExtension $extension */
        $extension = $this->container->getExtension('netgen_block_manager_admin');
        $extension->prepend($this->container);

        $config = array_merge_recursive(
            ...$this->container->getExtensionConfig('netgen_block_manager')
        );

        $this->assertInternalType('array', $config);

        $this->assertArrayHasKey('view', $config);

        $this->assertArrayHasKey('form_view', $config['view']);
        $this->assertArrayHasKey('admin', $config['view']['form_view']);

        $this->assertArrayHasKey('layout_view', $config['view']);
        $this->assertArrayHasKey('admin', $config['view']['layout_view']);

        $this->assertArrayHasKey('rule_condition_view', $config['view']);
        $this->assertArrayHasKey('value', $config['view']['rule_condition_view']);

        $this->assertArrayHasKey('default_view_templates', $config);
        $this->assertArrayHasKey('layout_view', $config['default_view_templates']);
        $this->assertArrayHasKey('admin', $config['default_view_templates']['layout_view']);
    }

    /**
     * Return an array of container extensions that need to be registered for
     * each test (usually just the container extension you are testing).
     *
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [
            new NetgenBlockManagerAdminExtension(),
        ];
    }
}
