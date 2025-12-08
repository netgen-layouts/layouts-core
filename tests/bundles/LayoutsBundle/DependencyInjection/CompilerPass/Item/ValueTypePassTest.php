<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\Item;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item\ValueTypePass;
use Netgen\Layouts\Exception\RuntimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

#[CoversClass(ValueTypePass::class)]
final class ValueTypePassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new ValueTypePass());
    }

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

    public function testProcessWithInvalidValueType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Value type "123_value_type" is not valid. Identifier must begin with a letter, followed by any combination of letters, digits and underscore.');

        $this->setParameter(
            'netgen_layouts.value_types',
            [
                '123_value_type' => [
                    'enabled' => true,
                    'manual_items' => false,
                ],
            ],
        );

        $this->container->setDefinition('netgen_layouts.item.registry.value_type', new Definition());

        $this->compile();
    }

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

    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
