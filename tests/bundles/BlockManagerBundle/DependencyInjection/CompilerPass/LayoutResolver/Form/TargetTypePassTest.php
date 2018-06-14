<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\LayoutResolver\Form;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\TargetTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class TargetTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\TargetTypePass::process
     */
    public function testProcess(): void
    {
        $formType = new Definition();
        $formType->addArgument([]);
        $this->setDefinition('netgen_block_manager.layout.resolver.form.target_type', $formType);

        $mapper = new Definition();
        $mapper->addTag('netgen_block_manager.layout.resolver.form.target_type.mapper', ['target_type' => 'target']);
        $this->setDefinition('netgen_block_manager.layout.resolver.form.target_type.mapper.test', $mapper);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.layout.resolver.form.target_type',
            0,
            [
                'target' => new Reference('netgen_block_manager.layout.resolver.form.target_type.mapper.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\TargetTypePass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Target type form mapper service tags should have an "target_type" attribute.
     */
    public function testProcessThrowsExceptionWithNoTagIdentifier(): void
    {
        $formType = new Definition();
        $formType->addArgument([]);
        $this->setDefinition('netgen_block_manager.layout.resolver.form.target_type', $formType);

        $mapper = new Definition();
        $mapper->addTag('netgen_block_manager.layout.resolver.form.target_type.mapper');
        $this->setDefinition('netgen_block_manager.layout.resolver.form.target_type.mapper.test', $mapper);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\TargetTypePass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        $this->assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TargetTypePass());
    }
}
