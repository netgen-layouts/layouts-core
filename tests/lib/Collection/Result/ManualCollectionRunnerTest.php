<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\ItemList;
use Netgen\Layouts\API\Values\Collection\SlotList;
use Netgen\Layouts\Collection\Item\VisibilityResolver;
use Netgen\Layouts\Collection\Result\CollectionRunnerFactory;
use Netgen\Layouts\Collection\Result\ManualCollectionRunner;
use Netgen\Layouts\Collection\Result\Result;
use Netgen\Layouts\Collection\Result\ResultSet;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\CmsItemBuilderInterface;
use Netgen\Layouts\Item\NullCmsItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

use function array_map;

#[CoversClass(ManualCollectionRunner::class)]
final class ManualCollectionRunnerTest extends TestCase
{
    private Stub&CmsItemBuilderInterface $cmsItemBuilderStub;

    protected function setUp(): void
    {
        $this->cmsItemBuilderStub = self::createStub(CmsItemBuilderInterface::class);
    }

    /**
     * @param mixed[] $itemValues
     * @param mixed[] $expected
     */
    #[DataProvider('manualCollectionDataProvider')]
    public function testCollectionResult(array $itemValues, array $expected, int $totalCount, int $offset = 0, int $limit = 200, int $flags = 0): void
    {
        $items = [];
        foreach ($itemValues as $position => $itemValue) {
            $items[$position] = Item::fromArray(
                [
                    'value' => $itemValue,
                    'cmsItem' => $itemValue !== null ?
                        CmsItem::fromArray(['value' => $itemValue, 'isVisible' => true]) :
                        new NullCmsItem('value'),
                    'position' => $position,
                ],
            );
        }

        $collection = Collection::fromArray(['items' => ItemList::fromArray($items), 'slots' => SlotList::fromArray([]), 'query' => null]);
        $factory = new CollectionRunnerFactory($this->cmsItemBuilderStub, new VisibilityResolver([]));
        $collectionRunner = $factory->getCollectionRunner($collection);

        self::assertSame($totalCount, $collectionRunner->count($collection));

        $result = array_map(
            static fn (Result $result): int|string|null => $result->item->value,
            [...$collectionRunner->runCollection($collection, $offset, $limit, $flags)],
        );

        self::assertSame($expected, $result);
    }

    /**
     * @return iterable<mixed>
     */
    public static function manualCollectionDataProvider(): iterable
    {
        return [
            [
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54],
                [42, 43, 44, 45, 46],
                13,
                0,
                5,
            ],
            [
                [42, 43, null, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54],
                [42, 43, 45, 46, 47],
                12,
                0,
                5,
            ],
            [
                [42, 43, null, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54],
                [42, 43, null, 45, 46, 47],
                12,
                0,
                5,
                ResultSet::INCLUDE_INVALID_ITEMS,
            ],
            [
                [42, 43, 44, 45, 46, 47, 48, 49, null, 51, 52, 53, 54],
                [42, 43, 44, 45, 46],
                12,
                0,
                5,
            ],
            [
                [42, 43, 44, 45, 46, 47, 48, 49, null, 51, 52, 53, 54],
                [42, 43, 44, 45, 46],
                12,
                0,
                5,
                ResultSet::INCLUDE_INVALID_ITEMS,
            ],
            [
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54],
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54],
                13,
            ],
            [
                [42, 43, 44, null, 46, 47, 48, 49, 50, 51, 52, 53, 54],
                [42, 43, 44, 46, 47, 48, 49, 50, 51, 52, 53, 54],
                12,
            ],
            [
                [42, 43, 44, null, 46, 47, 48, 49, 50, 51, 52, 53, 54],
                [42, 43, 44, null, 46, 47, 48, 49, 50, 51, 52, 53, 54],
                12,
                0,
                200,
                ResultSet::INCLUDE_INVALID_ITEMS,
            ],
            [
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54],
                [48, 49, 50, 51, 52],
                13,
                6,
                5,
            ],
            [
                [42, 43, 44, 45, 46, 47, 48, 49, null, 51, 52, 53, 54],
                [48, 49, 51, 52, 53],
                12,
                6,
                5,
            ],
            [
                [42, 43, 44, 45, 46, 47, 48, 49, null, 51, 52, 53, 54],
                [48, 49, null, 51, 52, 53],
                12,
                6,
                5,
                ResultSet::INCLUDE_INVALID_ITEMS,
            ],
            [
                [42, null, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54],
                [49, 50, 51, 52, 53],
                12,
                6,
                5,
            ],
            [
                [42, null, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54],
                [49, 50, 51, 52, 53],
                12,
                6,
                5,
                ResultSet::INCLUDE_INVALID_ITEMS,
            ],
            [
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, null, 54],
                [48, 49, 50, 51, 52],
                12,
                6,
                5,
            ],
            [
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, null, 54],
                [48, 49, 50, 51, 52],
                12,
                6,
                5,
                ResultSet::INCLUDE_INVALID_ITEMS,
            ],
            [
                [42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54],
                [48, 49, 50, 51, 52, 53, 54],
                13,
                6,
            ],
            [
                [42, 43, 44, 45, 46, 47, 48, 49, null, 51, 52, 53, 54],
                [48, 49, 51, 52, 53, 54],
                12,
                6,
            ],
            [
                [42, 43, 44, 45, 46, 47, 48, 49, null, 51, 52, 53, 54],
                [48, 49, null, 51, 52, 53, 54],
                12,
                6,
                200,
                ResultSet::INCLUDE_INVALID_ITEMS,
            ],
            [
                [42, null, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54],
                [49, 50, 51, 52, 53, 54],
                12,
                6,
            ],
            [
                [42, null, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54],
                [49, 50, 51, 52, 53, 54],
                12,
                6,
                200,
                ResultSet::INCLUDE_INVALID_ITEMS,
            ],
            [
                [],
                [],
                0,
            ],
            [
                [],
                [],
                0,
                5,
            ],
        ];
    }
}
