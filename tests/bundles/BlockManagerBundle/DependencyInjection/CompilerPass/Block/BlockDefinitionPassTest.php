<?php

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
     * @param string $handlerClass
     * @param string $definitionClass
     *
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::process
     * @dataProvider processDataProvider
     */
    public function testProcess($handlerClass, $definitionClass)
    {
        $this->setParameter('test.class', BlockDefinitionHandler::class);

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            ['block_definition' => ['enabled' => true]]
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_definition', new Definition());

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

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_definition',
            'addBlockDefinition',
            [
                'block_definition',
                new Reference('netgen_block_manager.block.block_definition.block_definition'),
            ]
        );
    }

    /**
     * @param string $handlerClass
     * @param string $definitionClass
     *
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::process
     * @dataProvider processDataProvider
     */
    public function testProcessWithCustomHandler($handlerClass, $definitionClass)
    {
        $this->setParameter('test.class', BlockDefinitionHandler::class);

        $this->setParameter(
            'netgen_block_manager.block_definitions',
            ['block_definition' => ['enabled' => true, 'handler' => 'custom']]
        );

        $this->setDefinition('netgen_block_manager.block.registry.block_definition', new Definition());

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

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.block_definition',
            'addBlockDefinition',
            [
                'block_definition',
                new Reference('netgen_block_manager.block.block_definition.block_definition'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Block definition handler definition must have an 'identifier' attribute in its' tag.
     */
    public function testProcessThrowsExceptionWithNoTagIdentifier()
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
    public function testProcessThrowsExceptionWithNoHandler()
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
    public function testProcessThrowsExceptionWithNoCustomHandler()
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
    public function testProcessWithEmptyContainer()
    {
        $this->compile();

        $this->assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    public function processDataProvider()
    {
        return [
            ['%test.class%', BlockDefinition::class],
            [BlockDefinitionHandler::class, BlockDefinition::class],
            [TwigBlockDefinitionHandlerInterface::class, TwigBlockDefinition::class],
            [ContainerDefinitionHandler::class, ContainerDefinition::class],
        ];
    }

    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new BlockDefinitionPass());
    }
}
