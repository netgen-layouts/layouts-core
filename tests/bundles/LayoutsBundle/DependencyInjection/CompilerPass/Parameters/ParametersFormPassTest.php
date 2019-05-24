<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\Parameters;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Parameters\ParametersFormPass;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ParametersFormPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Parameters\ParametersFormPass::process
     */
    public function testProcess(): void
    {
        $this->setDefinition('netgen_layouts.parameters.form.parameters', new Definition());

        $formMapper = new Definition();
        $formMapper->addTag(
            'netgen_layouts.parameter_type.form_mapper',
            ['type' => 'test']
        );

        $this->setDefinition('netgen_layouts.parameters.form.mapper.test', $formMapper);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.parameters.form.parameters',
            0,
            new Definition(
                ServiceLocator::class,
                [
                    [
                        'test' => new ServiceClosureArgument(new Reference('netgen_layouts.parameters.form.mapper.test')),
                    ],
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Parameters\ParametersFormPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ParametersFormPass());
    }
}
