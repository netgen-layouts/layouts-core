<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Layout;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class LayoutTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::buildLayoutTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::validateLayoutTypes
     */
    public function testProcess(): void
    {
        $this->setParameter('netgen_block_manager.block_definitions', []);
        $this->setParameter(
            'netgen_block_manager.layout_types',
            [
                'test' => [
                    'enabled' => true,
                    'zones' => [],
                ],
            ]
        );

        $this->container->setDefinition('netgen_block_manager.layout.registry.layout_type', new Definition(null, [[]]));

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_block_manager.layout.layout_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.layout.registry.layout_type',
            0,
            [
                'test' => new Reference('netgen_block_manager.layout.layout_type.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::buildLayoutTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::validateLayoutTypes
     */
    public function testProcessThrowsRuntimeExceptionWithNoBlockDefinition(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Block definition "title" used in "test" layout type does not exist.');

        $this->setParameter('netgen_block_manager.block_definitions', []);
        $this->setParameter(
            'netgen_block_manager.layout_types',
            [
                'test' => [
                    'enabled' => true,
                    'zones' => [
                        'zone' => [
                            'allowed_block_definitions' => ['title'],
                        ],
                    ],
                ],
            ]
        );

        $this->setDefinition('netgen_block_manager.layout.registry.layout_type', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new LayoutTypePass());
    }
}
