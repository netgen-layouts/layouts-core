<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\Block;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass;
use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class BlockTypePassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new BlockTypePass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcess(): void
    {
        $this->setParameter(
            'netgen_layouts.block_types',
            [
                'test' => [
                    'enabled' => true,
                    'icon' => '/icon.svg',
                    'definition_identifier' => 'test',
                ],
            ],
        );

        $this->setParameter(
            'netgen_layouts.block_definitions',
            [
                'test' => [
                    'name' => 'Test',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                    'defaults' => [],
                ],
            ],
        );

        $this->setDefinition('netgen_layouts.block.block_definition.test', new Definition());
        $this->setDefinition('netgen_layouts.block.registry.block_type', new Definition(null, [[]]));

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_layouts.block.block_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.block.registry.block_type',
            0,
            [
                'test' => new Reference('netgen_layouts.block.block_type.test'),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithRedefinedBlockType(): void
    {
        $this->setParameter(
            'netgen_layouts.block_types',
            [
                'test' => [
                    'enabled' => true,
                    'icon' => '/icon2.svg',
                    'definition_identifier' => 'other',
                ],
            ],
        );

        $this->setParameter(
            'netgen_layouts.block_definitions',
            [
                'test' => [
                    'name' => 'Test',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                    'defaults' => [],
                ],
                'other' => [
                    'name' => 'Other',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                    'defaults' => [],
                ],
            ],
        );

        $this->setDefinition('netgen_layouts.block.block_definition.test', new Definition());
        $this->setDefinition('netgen_layouts.block.block_definition.other', new Definition());
        $this->setDefinition('netgen_layouts.block.registry.block_type', new Definition(null, [[]]));

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_layouts.block_types');

        self::assertIsArray($blockTypes);
        self::assertArrayHasKey('test', $blockTypes);

        self::assertSame(
            [
                'enabled' => true,
                'icon' => '/icon2.svg',
                'definition_identifier' => 'other',
                'defaults' => [],
            ],
            $blockTypes['test'],
        );

        $this->assertContainerBuilderHasService('netgen_layouts.block.block_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.block.registry.block_type',
            0,
            [
                'test' => new Reference('netgen_layouts.block.block_type.test'),
                'other' => new Reference('netgen_layouts.block.block_type.other'),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithDefaultConfigForBlockType(): void
    {
        $this->setParameter(
            'netgen_layouts.block_types',
            [
                'test' => [],
            ],
        );

        $this->setParameter(
            'netgen_layouts.block_definitions',
            [
                'test' => [
                    'name' => 'Test',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                    'defaults' => [],
                ],
            ],
        );

        $this->setDefinition('netgen_layouts.block.block_definition.test', new Definition());
        $this->setDefinition('netgen_layouts.block.registry.block_type', new Definition(null, [[]]));

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_layouts.block_types');

        self::assertIsArray($blockTypes);
        self::assertArrayHasKey('test', $blockTypes);

        self::assertSame(
            [
                'enabled' => true,
                'name' => 'Test',
                'icon' => '/icon.svg',
                'defaults' => [],
                'definition_identifier' => 'test',
            ],
            $blockTypes['test'],
        );

        $this->assertContainerBuilderHasService('netgen_layouts.block.block_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.block.registry.block_type',
            0,
            [
                'test' => new Reference('netgen_layouts.block.block_type.test'),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithNonExistingBlockType(): void
    {
        $this->setParameter('netgen_layouts.block_types', []);

        $this->setParameter(
            'netgen_layouts.block_definitions',
            [
                'test' => [
                    'name' => 'Test',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                    'defaults' => [],
                ],
            ],
        );

        $this->setDefinition('netgen_layouts.block.block_definition.test', new Definition());
        $this->setDefinition('netgen_layouts.block.registry.block_type', new Definition(null, [[]]));

        $this->compile();

        /** @var array<string, mixed[]> $blockTypes */
        $blockTypes = $this->container->getParameter('netgen_layouts.block_types');
        self::assertArrayHasKey('test', $blockTypes);

        self::assertSame(
            [
                'name' => 'Test',
                'icon' => '/icon.svg',
                'enabled' => true,
                'defaults' => [],
                'definition_identifier' => 'test',
            ],
            $blockTypes['test'],
        );

        $this->assertContainerBuilderHasService('netgen_layouts.block.block_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.block.registry.block_type',
            0,
            [
                'test' => new Reference('netgen_layouts.block.block_type.test'),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithDisabledBlockType(): void
    {
        $this->setParameter(
            'netgen_layouts.block_types',
            [
                'type' => [
                    'enabled' => false,
                    'icon' => '/icon.svg',
                    'definition_identifier' => 'title',
                ],
            ],
        );

        $this->setParameter(
            'netgen_layouts.block_definitions',
            [
                'title' => [
                    'name' => 'Title',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                    'defaults' => [],
                ],
            ],
        );

        $this->setDefinition('netgen_layouts.block.block_definition.title', new Definition());
        $this->setDefinition('netgen_layouts.block.registry.block_type', new Definition(null, [[]]));

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_layouts.block_types');

        self::assertIsArray($blockTypes);
        self::assertArrayHasKey('type', $blockTypes);

        self::assertSame(
            [
                'enabled' => false,
                'icon' => '/icon.svg',
                'definition_identifier' => 'title',
                'defaults' => [],
            ],
            $blockTypes['type'],
        );

        $this->assertContainerBuilderHasService('netgen_layouts.block.block_type.type');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.block.registry.block_type',
            0,
            [
                'type' => new Reference('netgen_layouts.block.block_type.type'),
                'title' => new Reference('netgen_layouts.block.block_type.title'),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithDisabledBlockDefinition(): void
    {
        $this->setParameter(
            'netgen_layouts.block_types',
            [
                'title' => [
                    'enabled' => true,
                    'icon' => '/icon.svg',
                    'definition_identifier' => 'title',
                ],
            ],
        );

        $this->setParameter(
            'netgen_layouts.block_definitions',
            [
                'title' => [
                    'name' => 'Title',
                    'icon' => '/icon.svg',
                    'enabled' => false,
                    'defaults' => [],
                ],
            ],
        );

        $this->setDefinition('netgen_layouts.block.block_definition.title', new Definition());
        $this->setDefinition('netgen_layouts.block.registry.block_type', new Definition(null, [[]]));

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_layouts.block_types');

        self::assertIsArray($blockTypes);
        self::assertArrayHasKey('title', $blockTypes);

        self::assertSame(
            [
                'enabled' => false,
                'icon' => '/icon.svg',
                'definition_identifier' => 'title',
                'name' => 'Title',
                'defaults' => [],
            ],
            $blockTypes['title'],
        );

        $this->assertContainerBuilderHasService('netgen_layouts.block.block_type.title');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.block.registry.block_type',
            0,
            [
                'title' => new Reference('netgen_layouts.block.block_type.title'),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithDefaultIconTakenFromBlockDefinition(): void
    {
        $this->setParameter(
            'netgen_layouts.block_types',
            [
                'my_title' => [
                    'enabled' => true,
                    'icon' => null,
                    'definition_identifier' => 'title',
                ],
            ],
        );

        $this->setParameter(
            'netgen_layouts.block_definitions',
            [
                'title' => [
                    'name' => 'Title',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                    'defaults' => [],
                ],
            ],
        );

        $this->setDefinition('netgen_layouts.block.block_definition.title', new Definition());
        $this->setDefinition('netgen_layouts.block.registry.block_type', new Definition(null, [[]]));

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_layouts.block_types');

        self::assertIsArray($blockTypes);
        self::assertArrayHasKey('my_title', $blockTypes);

        self::assertSame(
            [
                'enabled' => true,
                'icon' => '/icon.svg',
                'definition_identifier' => 'title',
                'defaults' => [],
            ],
            $blockTypes['my_title'],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithDefaultsTakenFromBlockDefinition(): void
    {
        $this->setParameter(
            'netgen_layouts.block_types',
            [
                'my_title' => [
                    'enabled' => true,
                    'icon' => null,
                    'definition_identifier' => 'title',
                ],
            ],
        );

        $this->setParameter(
            'netgen_layouts.block_definitions',
            [
                'title' => [
                    'name' => 'Title',
                    'icon' => null,
                    'enabled' => true,
                    'defaults' => ['defaults'],
                ],
            ],
        );

        $this->setDefinition('netgen_layouts.block.block_definition.title', new Definition());
        $this->setDefinition('netgen_layouts.block.registry.block_type', new Definition(null, [[]]));

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_layouts.block_types');

        self::assertIsArray($blockTypes);
        self::assertArrayHasKey('my_title', $blockTypes);

        self::assertSame(
            [
                'enabled' => true,
                'icon' => null,
                'definition_identifier' => 'title',
                'defaults' => ['defaults'],
            ],
            $blockTypes['my_title'],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithDefaultsKeptFromBlockType(): void
    {
        $this->setParameter(
            'netgen_layouts.block_types',
            [
                'my_title' => [
                    'enabled' => true,
                    'icon' => null,
                    'definition_identifier' => 'title',
                    'defaults' => ['block_type_defaults'],
                ],
            ],
        );

        $this->setParameter(
            'netgen_layouts.block_definitions',
            [
                'title' => [
                    'name' => 'Title',
                    'icon' => null,
                    'enabled' => true,
                    'defaults' => ['block_definition_defaults'],
                ],
            ],
        );

        $this->setDefinition('netgen_layouts.block.block_definition.title', new Definition());
        $this->setDefinition('netgen_layouts.block.registry.block_type', new Definition(null, [[]]));

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_layouts.block_types');

        self::assertIsArray($blockTypes);
        self::assertArrayHasKey('my_title', $blockTypes);

        self::assertSame(
            [
                'enabled' => true,
                'icon' => null,
                'definition_identifier' => 'title',
                'defaults' => ['block_type_defaults'],
            ],
            $blockTypes['my_title'],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithDisabledBlockDefinitionAndAdditionalBlockType(): void
    {
        $this->setParameter(
            'netgen_layouts.block_types',
            [
                'type' => [
                    'enabled' => true,
                    'icon' => '/icon.svg',
                    'definition_identifier' => 'title',
                ],
            ],
        );

        $this->setParameter(
            'netgen_layouts.block_definitions',
            [
                'title' => [
                    'name' => 'Title',
                    'icon' => '/icon.svg',
                    'enabled' => false,
                    'defaults' => [],
                ],
            ],
        );

        $this->setDefinition('netgen_layouts.block.block_definition.title', new Definition());
        $this->setDefinition('netgen_layouts.block.registry.block_type', new Definition(null, [[]]));

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_layouts.block_types');

        self::assertIsArray($blockTypes);
        self::assertArrayHasKey('type', $blockTypes);

        self::assertSame(
            [
                'enabled' => false,
                'icon' => '/icon.svg',
                'definition_identifier' => 'title',
                'defaults' => [],
            ],
            $blockTypes['type'],
        );

        $this->assertContainerBuilderHasService('netgen_layouts.block.block_type.type');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.block.registry.block_type',
            0,
            [
                'type' => new Reference('netgen_layouts.block.block_type.type'),
                'title' => new Reference('netgen_layouts.block.block_type.title'),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessThrowsRuntimeExceptionWithNoBlockDefinition(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Block definition "title" used in "test" block type does not exist.');

        $this->setParameter(
            'netgen_layouts.block_types',
            [
                'test' => [
                    'enabled' => true,
                    'icon' => '/icon.svg',
                    'definition_identifier' => 'title',
                ],
            ],
        );

        $this->setParameter('netgen_layouts.block_definitions', []);

        $this->setDefinition('netgen_layouts.block.registry.block_type', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
