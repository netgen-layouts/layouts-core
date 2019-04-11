<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\LayoutResolver\Form;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\ConditionTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class ConditionTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\ConditionTypePass::process
     */
    public function testProcess(): void
    {
        $formType = new Definition();
        $formType->addArgument([]);
        $this->setDefinition('netgen_block_manager.layout.resolver.form.condition_type', $formType);

        $mapper = new Definition();
        $mapper->addTag('netgen_block_manager.layout.resolver.form.condition_type.mapper', ['condition_type' => 'condition']);
        $this->setDefinition('netgen_block_manager.layout.resolver.form.condition_type.mapper.test', $mapper);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.layout.resolver.form.condition_type',
            0,
            [
                'condition' => new Reference('netgen_block_manager.layout.resolver.form.condition_type.mapper.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\ConditionTypePass::process
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
