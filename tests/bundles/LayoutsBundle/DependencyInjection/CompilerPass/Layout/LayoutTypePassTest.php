<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\Layout;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass;
use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class LayoutTypePassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new LayoutTypePass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::buildLayoutTypes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::process
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::validateLayoutTypes
     */
    public function testProcess(): void
    {
        $this->setParameter('netgen_layouts.block_definitions', []);
        $this->setParameter(
            'netgen_layouts.layout_types',
            [
                'test' => [
                    'enabled' => true,
                    'zones' => [],
                ],
            ],
        );

        $this->container->setDefinition('netgen_layouts.layout.registry.layout_type', new Definition(null, [[]]));

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_layouts.layout.layout_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.layout.registry.layout_type',
            0,
            [
                'test' => new Reference('netgen_layouts.layout.layout_type.test'),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::buildLayoutTypes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::process
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::validateLayoutTypes
     */
    public function testProcessThrowsRuntimeExceptionWithNoBlockDefinition(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Block definition "title" used in "test" layout type does not exist.');

        $this->setParameter('netgen_layouts.block_definitions', []);
        $this->setParameter(
            'netgen_layouts.layout_types',
            [
                'test' => [
                    'enabled' => true,
                    'zones' => [
                        'zone' => [
                            'allowed_block_definitions' => ['title'],
                        ],
                    ],
                ],
            ],
        );

        $this->setDefinition('netgen_layouts.layout.registry.layout_type', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Layout\LayoutTypePass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
