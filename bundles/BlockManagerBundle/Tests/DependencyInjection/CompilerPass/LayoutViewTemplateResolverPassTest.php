<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutViewTemplateResolverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class LayoutViewTemplateResolverPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new LayoutViewTemplateResolverPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutViewTemplateResolverPass::process
     */
    public function testProcess()
    {
        $layoutTemplateResolver = new Definition();
        $layoutTemplateResolver->addArgument(array());
        $this->setDefinition('netgen_block_manager.view.template_resolver.layout_view', $layoutTemplateResolver);

        $layoutMatcher = new Definition();
        $layoutMatcher->addTag('netgen_block_manager.view.layout_matcher', array('identifier' => 'layout_id'));
        $this->setDefinition('netgen_block_manager.view.layout_matcher.test', $layoutMatcher);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.view.template_resolver.layout_view',
            0,
            array(
                'layout_id' => new Reference('netgen_block_manager.view.layout_matcher.test'),
            )
        );
    }
}
