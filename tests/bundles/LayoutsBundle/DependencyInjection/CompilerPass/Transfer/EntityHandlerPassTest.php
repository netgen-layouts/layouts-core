<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\Transfer;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Transfer\EntityHandlerPass;
use Netgen\Layouts\Exception\RuntimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\Argument\AbstractArgument;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

#[CoversClass(EntityHandlerPass::class)]
final class EntityHandlerPassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new EntityHandlerPass());
    }

    public function testProcess(): void
    {
        $this->setDefinition(
            'netgen_layouts.transfer.importer',
            new Definition(null, [[], [], new AbstractArgument()]),
        );

        $this->setDefinition(
            'netgen_layouts.transfer.serializer',
            new Definition(null, [[], new AbstractArgument()]),
        );

        $entityHandler = new Definition();
        $entityHandler->addTag('netgen_layouts.transfer.entity_handler', ['entity_type' => 'test']);
        $this->setDefinition('netgen_layouts.transfer.entity_handler.test', $entityHandler);

        $this->compile();

        $argument = new Definition(
            ServiceLocator::class,
            [
                [
                    'test' => new ServiceClosureArgument(new Reference('netgen_layouts.transfer.entity_handler.test')),
                ],
            ],
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.transfer.importer',
            2,
            $argument,
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.transfer.serializer',
            1,
            $argument,
        );
    }

    public function testProcessThrowsRuntimeExceptionWithInvalidValueTypeTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Entity type must begin with a letter and be followed by any combination of letters, digits and underscore.');

        $entityHandler = new Definition();
        $entityHandler->addTag('netgen_layouts.transfer.entity_handler', ['entity_type' => '123']);
        $this->setDefinition('netgen_layouts.transfer.entity_handler.test', $entityHandler);

        $this->compile();
    }

    public function testProcessThrowsRuntimeExceptionWithNoTagValueType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Entity handler service definition must have a \'entity_type\' attribute in its\' tag.');

        $entityHandler = new Definition();
        $entityHandler->addTag('netgen_layouts.transfer.entity_handler');
        $this->setDefinition('netgen_layouts.transfer.entity_handler.test', $entityHandler);

        $this->compile();
    }

    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
