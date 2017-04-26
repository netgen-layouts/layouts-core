<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\View;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\FragmentRendererPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class FragmentRendererPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\FragmentRendererPass::process
     */
    public function testProcess()
    {
        $fragmentRenderer = new Definition();
        $fragmentRenderer->addArgument(array());
        $fragmentRenderer->addArgument(array());
        $fragmentRenderer->addArgument(array());
        $fragmentRenderer->addArgument(array());

        $this->setDefinition('netgen_block_manager.view.renderer.fragment', $fragmentRenderer);

        $fragmentViewRenderer = new Definition();
        $fragmentViewRenderer->addTag('netgen_block_manager.view.fragment_view_renderer');
        $this->setDefinition('netgen_block_manager.view.renderer.fragment.test', $fragmentViewRenderer);

        $fragmentViewRenderer2 = new Definition();
        $fragmentViewRenderer2->addTag('netgen_block_manager.view.fragment_view_renderer');
        $this->setDefinition('netgen_block_manager.view.renderer.fragment.test2', $fragmentViewRenderer2);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.view.renderer.fragment',
            3,
            array(
                new Reference('netgen_block_manager.view.renderer.fragment.test'),
                new Reference('netgen_block_manager.view.renderer.fragment.test2'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\FragmentRendererPass::process
     * @doesNotPerformAssertions
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();
    }

    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FragmentRendererPass());
    }
}
