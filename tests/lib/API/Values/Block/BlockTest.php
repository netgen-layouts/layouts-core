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
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class BlockTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Block\Block::getAvailableLocales
     * @covers \Netgen\Layouts\API\Values\Block\Block::getCollection
     * @covers \Netgen\Layouts\API\Values\Block\Block::getCollections
     * @covers \Netgen\Layouts\API\Values\Block\Block::getDefinition
     * @covers \Netgen\Layouts\API\Values\Block\Block::getId
     * @covers \Netgen\Layouts\API\Values\Block\Block::getItemViewType
     * @covers \Netgen\Layouts\API\Values\Block\Block::getLayoutId
     * @covers \Netgen\Layouts\API\Values\Block\Block::getLocale
     * @covers \Netgen\Layouts\API\Values\Block\Block::getMainLocale
     * @covers \Netgen\Layouts\API\Values\Block\Block::getName
     * @covers \Netgen\Layouts\API\Values\Block\Block::getParentBlockId
     * @covers \Netgen\Layouts\API\Values\Block\Block::getParentPlaceholder
     * @covers \Netgen\Layouts\API\Values\Block\Block::getPlaceholder
     * @covers \Netgen\Layouts\API\Values\Block\Block::getPlaceholders
     * @covers \Netgen\Layouts\API\Values\Block\Block::getPosition
     * @covers \Netgen\Layouts\API\Values\Block\Block::getViewType
     * @covers \Netgen\Layouts\API\Values\Block\Block::hasCollection
     * @covers \Netgen\Layouts\API\Values\Block\Block::hasPlaceholder
     * @covers \Netgen\Layouts\API\Values\Block\Block::isAlwaysAvailable
     * @covers \Netgen\Layouts\API\Values\Block\Block::isTranslatable
     */
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
     * @covers \Netgen\Layouts\API\Values\Block\Block::buildDynamicParameters
     * @covers \Netgen\Layouts\API\Values\Block\Block::getDynamicParameter
     * @covers \Netgen\Layouts\API\Values\Block\Block::hasDynamicParameter
     */
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

    /**
     * @covers \Netgen\Layouts\API\Values\Block\Block::isContextual
     */
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
