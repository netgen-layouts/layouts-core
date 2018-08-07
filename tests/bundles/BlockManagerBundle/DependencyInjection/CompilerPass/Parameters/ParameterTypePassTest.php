<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Parameters;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\ParameterTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class ParameterTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\ParameterTypePass::process
     */
    public function testProcess(): void
    {
        $this->setDefinition('netgen_block_manager.parameters.registry.parameter_type', new Definition());

        $parameterType1 = new Definition();
        $parameterType1->addTag('netgen_block_manager.parameters.parameter_type');
        $this->setDefinition('netgen_block_manager.parameters.parameter_type.test1', $parameterType1);

        $parameterType2 = new Definition();
        $parameterType2->addTag('netgen_block_manager.parameters.parameter_type');
        $this->setDefinition('netgen_block_manager.parameters.parameter_type.test2', $parameterType2);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.parameters.registry.parameter_type',
            0,
            new Reference('netgen_block_manager.parameters.parameter_type.test1')
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.parameters.registry.parameter_type',
            1,
            new Reference('netgen_block_manager.parameters.parameter_type.test2')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\ParameterTypePass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ParameterTypePass());
    }
}
