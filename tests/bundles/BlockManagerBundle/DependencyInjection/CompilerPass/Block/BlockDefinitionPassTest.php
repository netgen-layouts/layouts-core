<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Block;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandler;
use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Block\TwigBlockDefinition;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class BlockDefinitionPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::process
     * @dataProvider processDataProvider
     */
    public function testProcess(string $handlerClass, string $definitionClass): void
    {
        $this->setParameter('test.class', BlockDefinitionHandler::class);

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            ['block_definition' => ['enabled' => true]]
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_definition', new Definition(null, [[]]));

        $blockDefinitionHandler = new Definition($handlerClass);
        $blockDefinitionHandler->addTag(
            'netgen_block_manager.block.block_definition_handler',
            ['identifier' => 'block_definition']
        );

        $this->setDefinition(
            'netgen_block_manager.block.block_definition.handler.test',
            $blockDefinitionHandler
        );

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_block_manager.block.block_definition.block_definition',
            $definitionClass
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.block.registry.block_definition',
            0,
            [
                'block_definition' => new Reference('netgen_block_manager.block.block_definition.block_definition'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::process
     * @dataProvider processDataProvider
     */
    public function testProcessWithCustomHandler(string $handlerClass, string $definitionClass): void
    {
        $this->setParameter('test.class', BlockDefinitionHandler::class);

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            ['block_definition' => ['enabled' => true, 'handler' => 'custom']]
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_definition', new Definition(null, [[]]));

        $blockDefinitionHandler = new Definition($handlerClass);
        $blockDefinitionHandler->addTag(
            'netgen_block_manager.block.block_definition_handler',
            ['identifier' => 'custom']
        );

        $this->setDefinition(
            'netgen_block_manager.block.block_definition.handler.test',
            $blockDefinitionHandler
        );

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_block_manager.block.block_definition.block_definition',
            $definitionClass
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.block.registry.block_definition',
            0,
            [
                'block_definition' => new Reference('netgen_block_manager.block.block_definition.block_definition'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Block definition handler definition must have an 'identifier' attribute in its' tag.
     */
    public function testProcessThrowsExceptionWithNoTagIdentifier(): void
    {
        $this->setParameter(
            'netgen_block_manager.block_definitions',
            ['block_definition' => ['enabled' => true]]
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_definition', new Definition());

        $blockDefinitionHandler = new Definition();
        $blockDefinitionHandler->addTag('netgen_block_manager.block.block_definition_handler');
        $this->setDefinition('netgen_block_manager.block.block_definition.handler.test', $blockDefinitionHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Block definition handler for "block_definition" block definition does not exist.
     */
    public function testProcessThrowsExceptionWithNoHandler(): void
    {
        $this->setParameter(
            'netgen_block_manager.block_definitions',
            ['block_definition' => ['enabled' => true]]
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_definition', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Block definition handler for "block_definition" block definition does not exist.
     */
    public function testProcessThrowsExceptionWithNoCustomHandler(): void
    {
        $this->setParameter(
            'netgen_block_manager.block_definitions',
            ['block_definition' => ['enabled' => true, 'handler' => 'custom']]
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_definition', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        $this->assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    public function processDataProvider(): array
    {
        return [
            ['%test.class%', BlockDefinition::class],
            [BlockDefinitionHandler::class, BlockDefinition::class],
            [TwigBlockDefinitionHandlerInterface::class, TwigBlockDefinition::class],
            [ContainerDefinitionHandler::class, ContainerDefinition::class],
        ];
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new BlockDefinitionPass());
    }
}
