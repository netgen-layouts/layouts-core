<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\LayoutResolver;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\LayoutResolverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class LayoutResolverPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new LayoutResolverPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\LayoutResolverPass::process
     */
    public function testProcess()
    {
        $layoutResolver = new Definition();
        $layoutResolver->addArgument(array());
        $layoutResolver->addArgument(array());
        $layoutResolver->addArgument(array());

        $this->setDefinition('netgen_block_manager.layout.resolver', $layoutResolver);

        $conditionMatcher = new Definition();
        $conditionMatcher->addTag(
            'netgen_block_manager.layout.resolver.condition_matcher',
            array('identifier' => 'test')
        );
        $this->setDefinition('netgen_block_manager.layout.resolver.condition_matcher.test', $conditionMatcher);

        $targetValueProvider = new Definition();
        $targetValueProvider->addTag(
            'netgen_block_manager.layout.resolver.target_value_provider',
            array('identifier' => 'test')
        );
        $this->setDefinition('netgen_block_manager.layout.resolver.target_value_provider.test', $targetValueProvider);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.layout.resolver',
            1,
            array(
                'test' => new Reference('netgen_block_manager.layout.resolver.target_value_provider.test'),
            )
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.layout.resolver',
            2,
            array(
                'test' => new Reference('netgen_block_manager.layout.resolver.condition_matcher.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\LayoutResolverPass::process
     * @expectedException \RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWhenNoIdentifierForConditionMatcher()
    {
        $this->setDefinition('netgen_block_manager.layout.resolver', new Definition());

        $conditionMatcher = new Definition();
        $conditionMatcher->addTag('netgen_block_manager.layout.resolver.condition_matcher');
        $this->setDefinition('netgen_block_manager.layout.resolver.condition_matcher.test', $conditionMatcher);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\LayoutResolverPass::process
     * @expectedException \RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWhenNoIdentifierForTargetValueProvider()
    {
        $this->setDefinition('netgen_block_manager.layout.resolver', new Definition());

        $targetValueProvider = new Definition();
        $targetValueProvider->addTag('netgen_block_manager.layout.resolver.target_value_provider');
        $this->setDefinition('netgen_block_manager.layout.resolver.target_value_provider.test', $targetValueProvider);

        $this->compile();
    }
}
