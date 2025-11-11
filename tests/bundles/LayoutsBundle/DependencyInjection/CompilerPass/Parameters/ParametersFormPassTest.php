<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\Parameters;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Parameters\ParametersFormPass;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

#[CoversClass(ParametersFormPass::class)]
final class ParametersFormPassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new ParametersFormPass());
    }

    public function testProcess(): void
    {
        $this->setDefinition('netgen_layouts.parameters.form.parameters', new Definition());

        $formMapper = new Definition();
        $formMapper->addTag(
            'netgen_layouts.parameter_type.form_mapper',
            ['type' => 'test'],
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
                ],
            ),
        );
    }

    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
