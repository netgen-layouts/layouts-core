<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\LayoutResolver\Form;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\ConditionTypePass;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ConditionTypePassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new ConditionTypePass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\ConditionTypePass::process
     */
    public function testProcess(): void
    {
        $formType = new Definition();
        $this->setDefinition('netgen_layouts.layout.resolver.form.condition_type', $formType);

        $mapper = new Definition();
        $mapper->addTag('netgen_layouts.condition_type.form_mapper', ['condition_type' => 'condition']);
        $this->setDefinition('netgen_layouts.layout.resolver.form.condition_type.mapper.test', $mapper);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.layout.resolver.form.condition_type',
            0,
            new Definition(
                ServiceLocator::class,
                [
                    [
                        'condition' => new ServiceClosureArgument(new Reference('netgen_layouts.layout.resolver.form.condition_type.mapper.test')),
                    ],
                ],
            ),
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
}
