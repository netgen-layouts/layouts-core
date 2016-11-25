<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\DependencyInjection;

use Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\NetgenBlockManagerAdminExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

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
}
