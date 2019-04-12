<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\Item;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item\CmsItemLoaderPass;
use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class CmsItemLoaderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item\CmsItemLoaderPass::process
     */
    public function testProcess(): void
    {
        $this->setDefinition(
            'netgen_layouts.item.item_loader',
            new Definition(null, [null, null])
        );

        $valueLoader = new Definition();
        $valueLoader->addTag('netgen_layouts.cms_value_loader', ['value_type' => 'test']);
        $this->setDefinition('netgen_layouts.item.value_loader.test', $valueLoader);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.item.item_loader',
            1,
            [
                'test' => new Reference('netgen_layouts.item.value_loader.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item\CmsItemLoaderPass::process
     */
    public function testProcessThrowsRuntimeExceptionWithInvalidValueTypeTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Value type must begin with a letter and be followed by any combination of letters, digits and underscore.');

        $this->setDefinition('netgen_layouts.item.item_loader', new Definition());

        $valueLoader = new Definition();
        $valueLoader->addTag('netgen_layouts.cms_value_loader', ['value_type' => '123']);
        $this->setDefinition('netgen_layouts.item.value_loader.test', $valueLoader);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item\CmsItemLoaderPass::process
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagValueType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Value loader service definition must have a \'value_type\' attribute in its\' tag.');

        $this->setDefinition('netgen_layouts.item.item_loader', new Definition());

        $valueLoader = new Definition();
        $valueLoader->addTag('netgen_layouts.cms_value_loader');
        $this->setDefinition('netgen_layouts.item.value_loader.test', $valueLoader);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item\CmsItemLoaderPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CmsItemLoaderPass());
    }
}
