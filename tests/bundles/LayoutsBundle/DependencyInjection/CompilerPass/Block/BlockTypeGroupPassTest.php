<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\Block;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

use function array_keys;

final class BlockTypeGroupPassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new BlockTypeGroupPass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::buildBlockTypeGroups
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::generateBlockTypeGroupConfig
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::process
     */
    public function testProcess(): void
    {
        $this->setParameter(
            'netgen_layouts.block_type_groups',
            [
                'test' => [
                    'enabled' => true,
                    'priority' => 0,
                    'block_types' => [],
                ],
            ],
        );

        $this->setParameter('netgen_layouts.block_types', []);

        $this->setDefinition('netgen_layouts.block.registry.block_type_group', new Definition(null, [[]]));

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_layouts.block.block_type_group.test');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.block.registry.block_type_group',
            0,
            [
                'test' => new Reference('netgen_layouts.block.block_type_group.test'),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::buildBlockTypeGroups
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::generateBlockTypeGroupConfig
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::process
     */
    public function testProcessWithNoBlockType(): void
    {
        $this->setParameter(
            'netgen_layouts.block_type_groups',
            [
                'test' => [
                    'enabled' => true,
                    'priority' => 0,
                    'block_types' => [
                        'test1' => [
                            'identifier' => 'test1',
                            'priority' => 0,
                        ],
                        'test2' => [
                            'identifier' => 'test2',
                            'priority' => 0,
                        ],
                    ],
                ],
            ],
        );

        $this->setParameter(
            'netgen_layouts.block_types',
            [
                'test1' => [
                    'enabled' => true,
                    'priority' => 0,
                ],
            ],
        );

        $this->setDefinition('netgen_layouts.block.block_type.test1', new Definition());
        $this->setDefinition('netgen_layouts.block.registry.block_type_group', new Definition(null, [[]]));

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_layouts.block.block_type_group.test');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.block.block_type_group.test',
            2,
            [
                new Reference('netgen_layouts.block.block_type.test1'),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::buildBlockTypeGroups
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::generateBlockTypeGroupConfig
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::process
     */
    public function testProcessWithPopulatingCustomGroup(): void
    {
        $this->setParameter(
            'netgen_layouts.block_type_groups',
            [
                'test' => [
                    'enabled' => true,
                    'priority' => 0,
                    'block_types' => [
                        'test1' => [
                            'identifier' => 'test1',
                            'priority' => 0,
                        ],
                    ],
                ],
                'custom' => [
                    'enabled' => true,
                    'priority' => 0,
                    'block_types' => [],
                ],
            ],
        );

        $this->setParameter(
            'netgen_layouts.block_types',
            [
                'test1' => [
                    'enabled' => true,
                    'definition_identifier' => 'test',
                    'priority' => 0,
                ],
                'test2' => [
                    'enabled' => false,
                    'definition_identifier' => 'test',
                    'priority' => 0,
                ],
                'test3' => [
                    'enabled' => true,
                    'definition_identifier' => 'test',
                    'priority' => 0,
                ],
            ],
        );

        $this->setDefinition('netgen_layouts.block.block_type.test1', new Definition());
        $this->setDefinition('netgen_layouts.block.block_type.test2', new Definition());
        $this->setDefinition('netgen_layouts.block.block_type.test3', new Definition());
        $this->setDefinition('netgen_layouts.block.registry.block_type_group', new Definition(null, [[]]));

        $this->compile();

        /** @var array<string, mixed[]> $blockTypeGroups */
        $blockTypeGroups = $this->container->getParameter('netgen_layouts.block_type_groups');
        self::assertArrayHasKey('custom', $blockTypeGroups);

        self::assertSame(
            [
                'enabled' => true,
                'priority' => 0,
                'block_types' => [
                    0 => [
                        'identifier' => 'test2',
                        'priority' => 0,
                    ],
                    1 => [
                        'identifier' => 'test3',
                        'priority' => 0,
                    ],
                ],
            ],
            $blockTypeGroups['custom'],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::buildBlockTypeGroups
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::generateBlockTypeGroupConfig
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::process
     */
    public function testProcessWithDisabledGroup(): void
    {
        $this->setParameter(
            'netgen_layouts.block_type_groups',
            [
                'test' => [
                    'enabled' => false,
                    'priority' => 0,
                    'block_types' => [],
                ],
            ],
        );

        $this->setParameter('netgen_layouts.block_types', []);

        $this->setDefinition('netgen_layouts.block.registry.block_type_group', new Definition(null, [[]]));

        $this->compile();

        /** @var array<string, mixed[]> $blockTypeGroups */
        $blockTypeGroups = $this->container->getParameter('netgen_layouts.block_type_groups');
        self::assertArrayHasKey('test', $blockTypeGroups);

        self::assertSame(
            [
                'enabled' => false,
                'priority' => 0,
                'block_types' => [],
            ],
            $blockTypeGroups['test'],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::buildBlockTypeGroups
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::generateBlockTypeGroupConfig
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::process
     */
    public function testProcessWithSortingGroups(): void
    {
        $this->setParameter(
            'netgen_layouts.block_type_groups',
            [
                'first' => [
                    'enabled' => true,
                    'priority' => 10,
                    'block_types' => [],
                ],
                'second' => [
                    'enabled' => true,
                    'priority' => 20,
                    'block_types' => [],
                ],
            ],
        );

        $this->setParameter('netgen_layouts.block_types', []);

        $this->setDefinition('netgen_layouts.block.registry.block_type_group', new Definition(null, [[]]));

        $this->compile();

        /** @var array<string, mixed[]> $blockTypeGroups */
        $blockTypeGroups = $this->container->getParameter('netgen_layouts.block_type_groups');

        self::assertSame(['second', 'first'], array_keys($blockTypeGroups));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypeGroupPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
