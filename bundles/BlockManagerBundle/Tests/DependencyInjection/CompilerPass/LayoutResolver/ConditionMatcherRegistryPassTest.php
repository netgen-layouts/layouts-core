<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\LayoutResolver;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\ConditionMatcherRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ConditionMatcherRegistryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConditionMatcherRegistryPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\ConditionMatcherRegistryPass::process
     */
    public function testProcess()
    {
        $conditionMatcherRegistry = new Definition();
        $this->setDefinition('netgen_block_manager.layout.resolver.condition_matcher.registry', $conditionMatcherRegistry);

        $conditionMatcher = new Definition();
        $conditionMatcher->addTag('netgen_block_manager.layout.resolver.condition_matcher');
        $this->setDefinition('netgen_block_manager.layout.resolver.condition_matcher.test', $conditionMatcher);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.layout.resolver.condition_matcher.registry',
            'addConditionMatcher',
            array(
                new Reference('netgen_block_manager.layout.resolver.condition_matcher.test'),
            )
        );
    }
}
