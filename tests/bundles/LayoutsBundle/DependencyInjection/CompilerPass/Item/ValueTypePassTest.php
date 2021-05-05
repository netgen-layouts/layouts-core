<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\Item;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item\ValueTypePass;
use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class ValueTypePassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new ValueTypePass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::buildValueTypes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::process
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::validateBrowserType
     */
    public function testProcess(): void
    {
        $this->setDefinition('netgen_content_browser.config.test', new Definition());

        $this->setParameter(
            'netgen_layouts.value_types',
            [
                'test' => [
                    'enabled' => true,
                    'manual_items' => true,
                ],
            ],
        );

        $this->container->setDefinition('netgen_layouts.item.registry.value_type', new Definition(null, [[]]));

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_layouts.item.value_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.item.registry.value_type',
            0,
            [
                'test' => new Reference('netgen_layouts.item.value_type.test'),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::buildValueTypes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::process
     */
    public function testProcessWithUnsupportedManualItems(): void
    {
        $this->setParameter(
            'netgen_layouts.value_types',
            [
                'test' => [
                    'enabled' => true,
                    'manual_items' => false,
                ],
            ],
        );

        $this->container->setDefinition('netgen_layouts.item.registry.value_type', new Definition(null, [[]]));

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_layouts.item.value_type.test');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.item.registry.value_type',
            0,
            [
                'test' => new Reference('netgen_layouts.item.value_type.test'),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::buildValueTypes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::process
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::validateBrowserType
     */
    public function testProcessWithInvalidBrowserType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Netgen Content Browser backend for "test" value type does not exist.');

        $this->setDefinition('netgen_content_browser.config.other', new Definition());

        $this->setParameter(
            'netgen_layouts.value_types',
            [
                'test' => [
                    'enabled' => true,
                    'manual_items' => true,
                ],
            ],
        );

        $this->container->setDefinition('netgen_layouts.item.registry.value_type', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item\ValueTypePass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
