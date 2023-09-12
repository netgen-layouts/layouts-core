<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\Block;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\Layouts\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\Layouts\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\Layouts\Block\ContainerDefinition;
use Netgen\Layouts\Block\TwigBlockDefinition;
use Netgen\Layouts\Exception\RuntimeException;
use stdClass;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class BlockDefinitionPassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new BlockDefinitionPass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::getConfigHandlers
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::process
     *
     * @dataProvider processDataProvider
     */
    public function testProcess(string $handlerClass, string $definitionClass): void
    {
        $this->setParameter('test.class', BlockDefinitionHandler::class);
        $this->setDefinition('netgen_layouts.block.block_definition_factory', new Definition());

        $this->setParameter(
            'netgen_layouts.block_definitions',
            ['block_definition' => ['enabled' => true]],
        );

        $this->setDefinition('netgen_layouts.block.registry.block_definition', new Definition(null, [[]]));

        $blockDefinitionHandler = new Definition($handlerClass);
        $blockDefinitionHandler->addTag(
            'netgen_layouts.block_definition_handler',
            ['identifier' => 'block_definition'],
        );

        $this->setDefinition(
            'netgen_layouts.block.block_definition.handler.test',
            $blockDefinitionHandler,
        );

        $configHandler = new Definition();
        $configHandler->addTag('netgen_layouts.block_config_handler', ['config_key' => 'key']);

        $this->setDefinition('netgen_layouts.block.block_config_handler.key', $configHandler);

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_layouts.block.block_definition.block_definition',
            $definitionClass,
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.block.block_definition.block_definition',
            4,
            [
                'key' => new Reference('netgen_layouts.block.block_config_handler.key'),
            ],
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.block.registry.block_definition',
            0,
            [
                'block_definition' => new Reference('netgen_layouts.block.block_definition.block_definition'),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::getConfigHandlers
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::process
     *
     * @dataProvider processDataProvider
     */
    public function testProcessWithCustomHandler(string $handlerClass, string $definitionClass): void
    {
        $this->setParameter('test.class', BlockDefinitionHandler::class);
        $this->setDefinition('netgen_layouts.block.block_definition_factory', new Definition());

        $this->setParameter(
            'netgen_layouts.block_definitions',
            ['block_definition' => ['enabled' => true, 'handler' => 'custom']],
        );

        $this->setDefinition('netgen_layouts.block.registry.block_definition', new Definition(null, [[]]));

        $blockDefinitionHandler = new Definition($handlerClass);
        $blockDefinitionHandler->addTag(
            'netgen_layouts.block_definition_handler',
            ['identifier' => 'custom'],
        );

        $this->setDefinition(
            'netgen_layouts.block.block_definition.handler.test',
            $blockDefinitionHandler,
        );

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_layouts.block.block_definition.block_definition',
            $definitionClass,
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.block.registry.block_definition',
            0,
            [
                'block_definition' => new Reference('netgen_layouts.block.block_definition.block_definition'),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::process
     */
    public function testProcessThrowsExceptionWithNoTagIdentifier(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Block definition handler for "block_definition" block definition does not exist.');

        $this->setParameter(
            'netgen_layouts.block_definitions',
            ['block_definition' => ['enabled' => true]],
        );

        $this->setDefinition('netgen_layouts.block.registry.block_definition', new Definition());

        $blockDefinitionHandler = new Definition(stdClass::class);
        $blockDefinitionHandler->addTag('netgen_layouts.block_definition_handler');
        $this->setDefinition('netgen_layouts.block.block_definition.handler.test', $blockDefinitionHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::getConfigHandlers
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::process
     */
    public function testProcessThrowsExceptionWithNoConfigKeyInTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Block config handler definition must have an \'config_key\' attribute in its\' tag.');

        $this->setParameter(
            'netgen_layouts.block_definitions',
            ['block_definition' => ['enabled' => true]],
        );

        $this->setDefinition('netgen_layouts.block.registry.block_definition', new Definition(null, [[]]));

        $blockDefinitionHandler = new Definition(stdClass::class);
        $blockDefinitionHandler->addTag(
            'netgen_layouts.block_definition_handler',
            ['identifier' => 'block_definition'],
        );

        $this->setDefinition(
            'netgen_layouts.block.block_definition.handler.test',
            $blockDefinitionHandler,
        );

        $configHandler = new Definition();
        $configHandler->addTag('netgen_layouts.block_config_handler');

        $this->setDefinition('netgen_layouts.block.block_config_handler.key', $configHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::process
     */
    public function testProcessThrowsExceptionWithNoHandler(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Block definition handler for "block_definition" block definition does not exist.');

        $this->setParameter(
            'netgen_layouts.block_definitions',
            ['block_definition' => ['enabled' => true]],
        );

        $this->setDefinition('netgen_layouts.block.registry.block_definition', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::process
     */
    public function testProcessThrowsExceptionWithNoCustomHandler(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Block definition handler for "block_definition" block definition does not exist.');

        $this->setParameter(
            'netgen_layouts.block_definitions',
            ['block_definition' => ['enabled' => true, 'handler' => 'custom']],
        );

        $this->setDefinition('netgen_layouts.block.registry.block_definition', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block\BlockDefinitionPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    public static function processDataProvider(): iterable
    {
        return [
            ['%test.class%', BlockDefinition::class],
            [BlockDefinitionHandler::class, BlockDefinition::class],
            [TwigBlockDefinitionHandlerInterface::class, TwigBlockDefinition::class],
            [ContainerDefinitionHandlerInterface::class, ContainerDefinition::class],
        ];
    }
}
