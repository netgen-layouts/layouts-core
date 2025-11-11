<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Exception\API\BlockException;
use Netgen\Layouts\Tests\Block\Stubs\BlockDefinitionHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[CoversClass(Block::class)]
final class BlockTest extends TestCase
{
    public function testSetProperties(): void
    {
        $definition = new BlockDefinition();

        $placeholder = Placeholder::fromArray(['identifier' => 'main']);
        $collection = Collection::fromArray(['id' => Uuid::uuid4()]);

        $blockUuid = Uuid::uuid4();
        $parentUuid = Uuid::uuid4();
        $layoutUuid = Uuid::uuid4();

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
                'placeholders' => [
                    'main' => $placeholder,
                ],
                'collections' => new ArrayCollection(
                    ['default' => $collection],
                ),
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'locale' => 'en',
                'parameters' => [],
            ],
        );

        self::assertSame($blockUuid->toString(), $block->getId()->toString());
        self::assertSame($layoutUuid->toString(), $block->getLayoutId()->toString());
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
        self::assertSame(3, $block->getPosition());
        self::assertInstanceOf(UuidInterface::class, $block->getParentBlockId());
        self::assertSame($parentUuid->toString(), $block->getParentBlockId()->toString());
        self::assertSame('main', $block->getParentPlaceholder());
        self::assertTrue($block->isTranslatable());
        self::assertSame('en', $block->getMainLocale());
        self::assertTrue($block->isAlwaysAvailable());
        self::assertSame(['en'], $block->getAvailableLocales());
        self::assertSame('en', $block->getLocale());

        self::assertCount(1, $block->getPlaceholders());
        self::assertSame($placeholder, $block->getPlaceholders()['main']);

        try {
            $block->getPlaceholder('test');
        } catch (BlockException) {
            // Do nothing
        }

        self::assertCount(1, $block->getCollections());
        self::assertSame($collection, $block->getCollections()['default']);

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
        $query = Block::fromArray(
            [
                'definition' => BlockDefinition::fromArray(
                    [
                        'handler' => new BlockDefinitionHandler(),
                    ],
                ),
            ],
        );

        self::assertFalse($query->isContextual());
    }
}
