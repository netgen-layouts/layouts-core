<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Block;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class BlockTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcess(): void
    {
        $this->setParameter(
            'netgen_block_manager.block_types',
            [
                'test' => [
                    'enabled' => true,
                    'icon' => '/icon.svg',
                    'definition_identifier' => 'test',
                ],
            ]
        );

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            [
                'test' => [
                    'name' => 'Test',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                ],
            ]
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_type',
            'addBlockType',
            [
                'test',
                new Reference('netgen_block_manager.block.block_type.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithRedefinedBlockType(): void
    {
        $this->setParameter(
            'netgen_block_manager.block_types',
            [
                'test' => [
                    'enabled' => true,
                    'icon' => '/icon2.svg',
                    'definition_identifier' => 'other',
                ],
            ]
        );

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            [
                'test' => [
                    'name' => 'Test',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                ],
                'other' => [
                    'name' => 'Other',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                ],
            ]
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_block_manager.block_types');

        $this->assertInternalType('array', $blockTypes);
        $this->assertArrayHasKey('test', $blockTypes);

        $this->assertEquals(
            [
                'enabled' => true,
                'icon' => '/icon2.svg',
                'definition_identifier' => 'other',
            ],
            $blockTypes['test']
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_type',
            'addBlockType',
            [
                'test',
                new Reference('netgen_block_manager.block.block_type.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithDefaultConfigForBlockType(): void
    {
        $this->setParameter(
            'netgen_block_manager.block_types',
            [
                'test' => [],
            ]
        );

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            [
                'test' => [
                    'name' => 'Test',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                ],
            ]
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_block_manager.block_types');

        $this->assertInternalType('array', $blockTypes);
        $this->assertArrayHasKey('test', $blockTypes);

        $this->assertEquals(
            [
                'name' => 'Test',
                'icon' => '/icon.svg',
                'enabled' => true,
                'definition_identifier' => 'test',
            ],
            $blockTypes['test']
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_type',
            'addBlockType',
            [
                'test',
                new Reference('netgen_block_manager.block.block_type.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithNonExistingBlockType(): void
    {
        $this->setParameter('netgen_block_manager.block_types', []);

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            [
                'test' => [
                    'name' => 'Test',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                ],
            ]
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_block_manager.block_types');
        $this->assertArrayHasKey('test', $blockTypes);

        $this->assertEquals(
            [
                'name' => 'Test',
                'icon' => '/icon.svg',
                'enabled' => true,
                'definition_identifier' => 'test',
                'defaults' => [],
            ],
            $blockTypes['test']
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_type',
            'addBlockType',
            [
                'test',
                new Reference('netgen_block_manager.block.block_type.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithDisabledBlockType(): void
    {
        $this->setParameter(
            'netgen_block_manager.block_types',
            [
                'type' => [
                    'enabled' => false,
                    'icon' => '/icon.svg',
                    'definition_identifier' => 'title',
                ],
            ]
        );

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            [
                'title' => [
                    'name' => 'Title',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                ],
            ]
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_block_manager.block_types');

        $this->assertInternalType('array', $blockTypes);
        $this->assertArrayHasKey('type', $blockTypes);

        $this->assertEquals(
            [
                'enabled' => false,
                'icon' => '/icon.svg',
                'definition_identifier' => 'title',
            ],
            $blockTypes['type']
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_type.type');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_type',
            'addBlockType',
            [
                'type',
                new Reference('netgen_block_manager.block.block_type.type'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithDisabledBlockDefinition(): void
    {
        $this->setParameter(
            'netgen_block_manager.block_types',
            [
                'title' => [
                    'enabled' => true,
                    'icon' => '/icon.svg',
                    'definition_identifier' => 'title',
                ],
            ]
        );

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            [
                'title' => [
                    'name' => 'Title',
                    'icon' => '/icon.svg',
                    'enabled' => false,
                ],
            ]
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_block_manager.block_types');

        $this->assertInternalType('array', $blockTypes);
        $this->assertArrayHasKey('title', $blockTypes);

        $this->assertEquals(
            [
                'enabled' => false,
                'icon' => '/icon.svg',
                'definition_identifier' => 'title',
                'name' => 'Title',
            ],
            $blockTypes['title']
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_type.title');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_type',
            'addBlockType',
            [
                'title',
                new Reference('netgen_block_manager.block.block_type.title'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     */
    public function testProcessWithDisabledBlockDefinitionAndAdditionalBlockType(): void
    {
        $this->setParameter(
            'netgen_block_manager.block_types',
            [
                'type' => [
                    'enabled' => true,
                    'icon' => '/icon.svg',
                    'definition_identifier' => 'title',
                ],
            ]
        );

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            [
                'title' => [
                    'name' => 'Title',
                    'icon' => '/icon.svg',
                    'enabled' => false,
                ],
            ]
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();

        $blockTypes = $this->container->getParameter('netgen_block_manager.block_types');

        $this->assertInternalType('array', $blockTypes);
        $this->assertArrayHasKey('type', $blockTypes);

        $this->assertEquals(
            [
                'enabled' => false,
                'icon' => '/icon.svg',
                'definition_identifier' => 'title',
            ],
            $blockTypes['type']
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_type.type');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_type',
            'addBlockType',
            [
                'type',
                new Reference('netgen_block_manager.block.block_type.type'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::buildBlockTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::generateBlockTypeConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::validateBlockTypes
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Block definition "title" used in "test" block type does not exist.
     */
    public function testProcessThrowsRuntimeExceptionWithNoBlockDefinition(): void
    {
        $this->setParameter(
            'netgen_block_manager.block_types',
            [
                'test' => [
                    'enabled' => true,
                    'icon' => '/icon.svg',
                    'definition_identifier' => 'title',
                ],
            ]
        );

        $this->setParameter('netgen_block_manager.block_definitions', []);

        $this->setDefinition('netgen_block_manager.block.registry.block_type', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockTypePass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        $this->assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new BlockTypePass());
    }
}
