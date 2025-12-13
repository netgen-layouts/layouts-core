<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\API\Values\Block\PlaceholderList;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\CollectionList;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Exception\API\BlockException;
use Netgen\Layouts\Parameters\ParameterList;
use Netgen\Layouts\Tests\Block\Stubs\BlockDefinitionHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(Block::class)]
final class BlockTest extends TestCase
{
    public function testSetProperties(): void
    {
        $definition = new BlockDefinition();

        $placeholder = Placeholder::fromArray(['identifier' => 'main']);
        $collection = Collection::fromArray(['id' => Uuid::v4()]);

        $blockUuid = Uuid::v4();
        $parentUuid = Uuid::v4();
        $layoutUuid = Uuid::v4();

        $block = Block::fromArray(
            [
                'id' => $blockUuid,
                'layoutId' => $layoutUuid,
                'definition' => $definition,
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'position' => 3,
                'parentBlockId' => $parentUuid,
                'parentPlaceholder' => 'main',
                'placeholders' => new PlaceholderList(['main' => $placeholder]),
                'collections' => CollectionList::fromArray(['default' => $collection]),
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'isAlwaysAvailable' => true,
                'availableLocales' => ['en'],
                'locale' => 'en',
                'parameters' => new ParameterList(),
            ],
        );

        self::assertSame($blockUuid->toString(), $block->id->toString());
        self::assertSame($layoutUuid->toString(), $block->layoutId->toString());
        self::assertSame($definition, $block->definition);
        self::assertSame($placeholder, $block->getPlaceholder('main'));
        self::assertFalse($block->hasPlaceholder('test'));
        self::assertTrue($block->hasPlaceholder('main'));
        self::assertSame($collection, $block->getCollection('default'));
        self::assertFalse($block->hasCollection('test'));
        self::assertTrue($block->hasCollection('default'));
        self::assertSame('default', $block->viewType);
        self::assertSame('standard', $block->itemViewType);
        self::assertSame('My block', $block->name);
        self::assertSame(3, $block->position);
        self::assertInstanceOf(Uuid::class, $block->parentBlockId);
        self::assertSame($parentUuid->toString(), $block->parentBlockId->toString());
        self::assertSame('main', $block->parentPlaceholder);
        self::assertTrue($block->isTranslatable);
        self::assertSame('en', $block->mainLocale);
        self::assertTrue($block->isAlwaysAvailable);
        self::assertSame(['en'], $block->availableLocales);
        self::assertSame('en', $block->locale);

        self::assertCount(1, $block->placeholders);
        self::assertSame($placeholder, $block->placeholders['main']);

        try {
            $block->getPlaceholder('test');
        } catch (BlockException) {
            // Do nothing
        }

        self::assertCount(1, $block->collections);
        self::assertSame($collection, $block->collections['default']);

        try {
            $block->getCollection('test');
        } catch (BlockException) {
            // Do nothing
        }
    }

    public function testGetDynamicParameter(): void
    {
        $block = Block::fromArray(
            [
                'definition' => BlockDefinition::fromArray(
                    [
                        'handler' => new BlockDefinitionHandler(),
                    ],
                ),
            ],
        );

        self::assertTrue($block->hasDynamicParameter('definition_param'));
        self::assertSame('definition_value', $block->getDynamicParameter('definition_param'));

        self::assertTrue($block->hasDynamicParameter('closure_param'));
        self::assertSame('closure_value', $block->getDynamicParameter('closure_param'));

        self::assertNull($block->getDynamicParameter('unknown_param'));
        self::assertFalse($block->hasDynamicParameter('unknown_param'));
    }

    public function testIsContextual(): void
    {
        $block = Block::fromArray(
            [
                'definition' => BlockDefinition::fromArray(
                    [
                        'handler' => new BlockDefinitionHandler(),
                    ],
                ),
            ],
        );

        self::assertFalse($block->isContextual);
    }
}
