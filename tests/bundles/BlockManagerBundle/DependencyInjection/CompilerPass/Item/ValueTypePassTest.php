<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Item;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class ValueTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::buildValueTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::validateBrowserType
     */
    public function testProcess(): void
    {
        $this->setDefinition('netgen_content_browser.config.test', new Definition());

        $this->setParameter(
            'netgen_block_manager.value_types',
            [
                'test' => [
                    'enabled' => true,
                ],
            ]
        );

        $this->container->setDefinition('netgen_block_manager.item.registry.value_type', new Definition(null, [[]]));

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_block_manager.item.value_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.item.registry.value_type',
            0,
            [
                'test' => new Reference('netgen_block_manager.item.value_type.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::buildValueTypes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::validateBrowserType
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Netgen Content Browser backend for "test" value type does not exist.
     */
    public function testProcessWithInvalidBrowserType(): void
    {
        $this->setDefinition('netgen_content_browser.config.other', new Definition());

        $this->setParameter(
            'netgen_block_manager.value_types',
            [
                'test' => [
                    'enabled' => true,
                ],
            ]
        );

        $this->container->setDefinition('netgen_block_manager.item.registry.value_type', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ValueTypePass());
    }
}
