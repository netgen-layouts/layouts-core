<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\DependencyInjection;

use Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\NetgenBlockManagerAdminExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NetgenBlockManagerAdminExtensionTest extends AbstractExtensionTestCase
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
            new NetgenBlockManagerAdminExtension(),
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\NetgenBlockManagerAdminExtension::load
     */
    public function testParameters()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('netgen_block_manager.admin.csrf_token_id', 'ngbm_admin');
        $this->assertContainerBuilderHasParameter('netgen_block_manager.app.block_edit.default_browser_config', 'ezlocation');
        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.admin.pagelayout',
            'NetgenBlockManagerAdminBundle:admin:pagelayout.html.twig'
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
        $this->assertContainerBuilderHasService('netgen_block_manager.templating.twig.extension.admin');
        $this->assertContainerBuilderHasService('netgen_block_manager.event_listener.set_is_admin_request');
        $this->assertContainerBuilderHasService('netgen_block_manager.controller.admin.layouts');
    }

    /**
     * We test for existence of one config value from each of the config files.
     *
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\NetgenBlockManagerAdminExtension::prepend
     */
    public function testPrepend()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.bundles', array('NetgenBlockManagerBundle' => true));
        $container->registerExtension(new NetgenBlockManagerExtension());
        $extension = new NetgenBlockManagerAdminExtension();

        $extension->prepend($container);

        $config = call_user_func_array(
            'array_merge_recursive',
            $container->getExtensionConfig('netgen_block_manager')
        );

        $this->assertInternalType('array', $config);

        $this->assertArrayHasKey('view', $config);

        $this->assertArrayHasKey('form_view', $config['view']);
        $this->assertArrayHasKey('admin', $config['view']['form_view']);

        $this->assertArrayHasKey('layout_view', $config['view']);
        $this->assertArrayHasKey('admin', $config['view']['layout_view']);

        $this->assertArrayHasKey('rule_condition_view', $config['view']);
        $this->assertArrayHasKey('admin', $config['view']['rule_condition_view']);

        $this->assertArrayHasKey('rule_target_view', $config['view']);
        $this->assertArrayHasKey('admin', $config['view']['rule_target_view']);

        $this->assertArrayHasKey('default_view_templates', $config);
        $this->assertArrayHasKey('layout_view', $config['default_view_templates']);
        $this->assertArrayHasKey('admin', $config['default_view_templates']['layout_view']);
    }
}
