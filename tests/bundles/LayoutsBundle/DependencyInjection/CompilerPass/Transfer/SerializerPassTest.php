<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\Transfer;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Transfer\SerializerPass;
use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class SerializerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Transfer\SerializerPass::process
     */
    public function testProcess(): void
    {
        $this->setDefinition(
            'netgen_layouts.transfer.serializer',
            new Definition(null, [[]])
        );

        $entityLoader = new Definition();
        $entityLoader->addTag('netgen_layouts.transfer.entity_loader', ['entity_type' => 'test']);
        $this->setDefinition('netgen_layouts.transfer.entity_loader.test', $entityLoader);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.transfer.serializer',
            1,
            new Definition(
                ServiceLocator::class,
                [
                    [
                        'test' => new ServiceClosureArgument(new Reference('netgen_layouts.transfer.entity_loader.test')),
                    ],
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Transfer\SerializerPass::process
     */
    public function testProcessThrowsRuntimeExceptionWithInvalidValueTypeTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Entity type must begin with a letter and be followed by any combination of letters, digits and underscore.');

        $this->setDefinition('netgen_layouts.transfer.serializer', new Definition());

        $entityLoader = new Definition();
        $entityLoader->addTag('netgen_layouts.transfer.entity_loader', ['entity_type' => '123']);
        $this->setDefinition('netgen_layouts.transfer.entity_loader.test', $entityLoader);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Transfer\SerializerPass::process
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagValueType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Entity loader service definition must have a \'entity_type\' attribute in its\' tag.');

        $this->setDefinition('netgen_layouts.transfer.serializer', new Definition());

        $entityLoader = new Definition();
        $entityLoader->addTag('netgen_layouts.transfer.entity_loader');
        $this->setDefinition('netgen_layouts.transfer.entity_loader.test', $entityLoader);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Transfer\SerializerPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new SerializerPass());
    }
}
