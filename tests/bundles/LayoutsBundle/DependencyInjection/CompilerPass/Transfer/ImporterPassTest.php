<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\Transfer;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Transfer\ImporterPass;
use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ImporterPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Transfer\ImporterPass::process
     */
    public function testProcess(): void
    {
        $this->setDefinition(
            'netgen_layouts.transfer.input.importer',
            new Definition(null, [[]])
        );

        $entityImporter = new Definition();
        $entityImporter->addTag('netgen_layouts.transfer.entity_importer', ['entity_type' => 'test']);
        $this->setDefinition('netgen_layouts.transfer.entity_importer.test', $entityImporter);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.transfer.input.importer',
            1,
            new Definition(
                ServiceLocator::class,
                [
                    [
                        'test' => new ServiceClosureArgument(new Reference('netgen_layouts.transfer.entity_importer.test')),
                    ],
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Transfer\ImporterPass::process
     */
    public function testProcessThrowsRuntimeExceptionWithInvalidValueTypeTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Entity type must begin with a letter and be followed by any combination of letters, digits and underscore.');

        $this->setDefinition('netgen_layouts.transfer.input.importer', new Definition());

        $entityImporter = new Definition();
        $entityImporter->addTag('netgen_layouts.transfer.entity_importer', ['entity_type' => '123']);
        $this->setDefinition('netgen_layouts.transfer.entity_importer.test', $entityImporter);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Transfer\ImporterPass::process
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagValueType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Entity importer service definition must have a \'entity_type\' attribute in its\' tag.');

        $this->setDefinition('netgen_layouts.transfer.input.importer', new Definition());

        $entityImporter = new Definition();
        $entityImporter->addTag('netgen_layouts.transfer.entity_importer');
        $this->setDefinition('netgen_layouts.transfer.entity_importer.test', $entityImporter);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Transfer\ImporterPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ImporterPass());
    }
}
