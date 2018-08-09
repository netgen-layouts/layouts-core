<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Exception\Core\BlockException;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use PHPUnit\Framework\TestCase;

final class BlockTest extends TestCase
{
    public function testInstance(): void
    {
        self::assertInstanceOf(Value::class, new Block());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getCollections
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholders
     */
    public function testDefaultProperties(): void
    {
        $block = new Block();

        self::assertCount(0, $block->getPlaceholders());
        self::assertCount(0, $block->getCollections());
        self::assertSame([], $block->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getCollection
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getCollections
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getDefinition
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getId
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getItemViewType
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getLocale
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getMainLocale
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getName
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholder
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholders
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getViewType
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasCollection
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasPlaceholder
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isAlwaysAvailable
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isTranslatable
     */
    public function testSetProperties(): void
    {
        $definition = new BlockDefinition();

        $placeholder = Placeholder::fromArray(['identifier' => 'main']);
        $collection = Collection::fromArray(['id' => 42]);

        $block = Block::fromArray(
            [
                'id' => 42,
                'layoutId' => 24,
                'definition' => $definition,
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'parentPosition' => 3,
                'placeholders' => [
                    'main' => $placeholder,
                ],
                'collections' => new ArrayCollection(
                    ['default' => $collection]
                ),
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'locale' => 'en',
                'parameters' => [],
            ]
        );

        self::assertSame(42, $block->getId());
        self::assertSame(24, $block->getLayoutId());
        self::assertSame($definition, $block->getDefinition());
        self::assertSame($placeholder, $block->getPlaceholder('main'));
        self::assertFalse($block->hasPlaceholder('test'));
        self::assertTrue($block->hasPlaceholder('main'));
        self::assertSame($collection, $block->getCollection('default'));
        self::assertFalse($block->hasCollection('test'));
        self::assertTrue($block->hasCollection('default'));
        self::assertSame('default', $block->getViewType());
        self::assertSame('standard', $block->getItemViewType());
        self::assertSame('My block', $block->getName());
        self::assertSame(3, $block->getParentPosition());
        self::assertTrue($block->isTranslatable());
        self::assertSame('en', $block->getMainLocale());
        self::assertTrue($block->isAlwaysAvailable());
        self::assertSame(['en'], $block->getAvailableLocales());
        self::assertSame('en', $block->getLocale());

        self::assertCount(1, $block->getPlaceholders());
        self::assertSame($placeholder, $block->getPlaceholders()['main']);

        try {
            $block->getPlaceholder('test');
        } catch (BlockException $e) {
            // Do nothing
        }

        self::assertCount(1, $block->getCollections());
        self::assertSame($collection, $block->getCollections()['default']);

        try {
            $block->getCollection('test');
        } catch (BlockException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::buildDynamicParameters
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getDynamicParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasDynamicParameter
     */
    public function testGetDynamicParameter(): void
    {
        $block = Block::fromArray(
            [
                'definition' => BlockDefinition::fromArray(
                    [
                        'handler' => new BlockDefinitionHandler(),
                    ]
                ),
            ]
        );

        self::assertTrue($block->hasDynamicParameter('definition_param'));
        self::assertSame('definition_value', $block->getDynamicParameter('definition_param'));

        self::assertTrue($block->hasDynamicParameter('closure_param'));
        self::assertSame('closure_value', $block->getDynamicParameter('closure_param'));

        self::assertNull($block->getDynamicParameter('unknown_param'));
        self::assertFalse($block->hasDynamicParameter('unknown_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isContextual
     */
    public function testIsContextual(): void
    {
        $query = Block::fromArray(
            [
                'definition' => BlockDefinition::fromArray(
                    [
                        'handler' => new BlockDefinitionHandler(),
                    ]
                ),
            ]
        );

        self::assertFalse($query->isContextual());
    }
}
