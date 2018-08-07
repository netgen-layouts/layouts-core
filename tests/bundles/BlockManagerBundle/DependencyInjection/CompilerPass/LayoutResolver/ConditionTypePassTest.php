<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\LayoutResolver;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\ConditionTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class ConditionTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\ConditionTypePass::process
     */
    public function testProcess(): void
    {
        $this->setDefinition('netgen_block_manager.layout.resolver.registry.condition_type', new Definition());

        $conditionType1 = new Definition();
        $conditionType1->addTag('netgen_block_manager.layout.resolver.condition_type');
        $this->setDefinition('netgen_block_manager.layout.resolver.condition_type.test1', $conditionType1);

        $conditionType2 = new Definition();
        $conditionType2->addTag('netgen_block_manager.layout.resolver.condition_type');
        $this->setDefinition('netgen_block_manager.layout.resolver.condition_type.test2', $conditionType2);

        $this->compile();

        self::assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.layout.resolver.registry.condition_type',
            0,
            new Reference('netgen_block_manager.layout.resolver.condition_type.test1')
        );

        self::assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.layout.resolver.registry.condition_type',
            1,
            new Reference('netgen_block_manager.layout.resolver.condition_type.test2')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\ConditionTypePass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ConditionTypePass());
    }
}
