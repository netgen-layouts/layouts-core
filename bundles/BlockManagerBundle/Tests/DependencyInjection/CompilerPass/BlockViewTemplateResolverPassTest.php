<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\BlockViewTemplateResolverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class BlockViewTemplateResolverPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new BlockViewTemplateResolverPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\BlockViewTemplateResolverPass::process
     */
    public function testProcess()
    {
        $blockTemplateResolver = new Definition();
        $blockTemplateResolver->addArgument(array());
        $this->setDefinition('netgen_block_manager.view.template_resolver.block', $blockTemplateResolver);

        $blockMatcher = new Definition();
        $blockMatcher->addTag('netgen_block_manager.view.block_matcher', array('identifier' => 'block_id'));
        $this->setDefinition('netgen_block_manager.view.block_matcher.test', $blockMatcher);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.view.template_resolver.block',
            0,
            array(
                'block_id' => new Reference('netgen_block_manager.view.block_matcher.test'),
            )
        );
    }
}
