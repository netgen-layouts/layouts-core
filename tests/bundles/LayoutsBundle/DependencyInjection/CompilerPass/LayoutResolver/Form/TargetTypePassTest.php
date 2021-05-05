<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\LayoutResolver\Form;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\TargetTypePass;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class TargetTypePassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new TargetTypePass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\TargetTypePass::process
     */
    public function testProcess(): void
    {
        $formType = new Definition();
        $this->setDefinition('netgen_layouts.layout.resolver.form.target_type', $formType);

        $mapper = new Definition();
        $mapper->addTag('netgen_layouts.target_type.form_mapper', ['target_type' => 'target']);
        $this->setDefinition('netgen_layouts.layout.resolver.form.target_type.mapper.test', $mapper);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.layout.resolver.form.target_type',
            0,
            new Definition(
                ServiceLocator::class,
                [
                    [
                        'target' => new ServiceClosureArgument(new Reference('netgen_layouts.layout.resolver.form.target_type.mapper.test')),
                    ],
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\TargetTypePass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
