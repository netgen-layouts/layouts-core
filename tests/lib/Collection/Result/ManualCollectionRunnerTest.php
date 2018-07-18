<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Item\VisibilityResolver;
use Netgen\BlockManager\Collection\Result\CollectionRunnerFactory;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\CmsItemBuilderInterface;
use Netgen\BlockManager\Tests\Collection\Stubs\Collection;
use PHPUnit\Framework\TestCase;

final class ManualCollectionRunnerTest extends TestCase
{
    use IteratorTestTrait;

    /**
     * @var \Netgen\BlockManager\Item\CmsItemBuilderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $cmsItemBuilderMock;

    public function setUp(): void
    {
        $this->cmsItemBuilderMock = $this->createMock(CmsItemBuilderInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ManualCollectionRunner::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ManualCollectionRunner::count
     * @covers \Netgen\BlockManager\Collection\Result\ManualCollectionRunner::runCollection
     *
     * @dataProvider manualCollectionProvider
     */
    public function testCollectionResult(array $collectionItems, array $values, int $totalCount, int $offset = 0, int $limit = 200, int $flags = 0): void
    {
        $collection = new Collection($collectionItems);
        $factory = new CollectionRunnerFactory($this->cmsItemBuilderMock, new VisibilityResolver());
        $collectionRunner = $factory->getCollectionRunner($collection);
        $expectedValues = $this->buildExpectedValues($values);

        $this->assertSame($totalCount, $collectionRunner->count($collection));
        $this->assertIteratorValues(
            $expectedValues,
            $collectionRunner->runCollection($collection, $offset, $limit, $flags)
        );
    }

    /**
     * Builds data providers for building result from manual collection.
     *
     * IDs are identifiers of 3rd party values (for example eZ content)
     */
    public function manualCollectionProvider(): array
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

    private function buildExpectedValues(array $values): array
    {
        $results = [];
        foreach ($values as $key => $value) {
            $results[] = new Result($key, CmsItem::fromArray(['value' => $value]));
        }

        return $results;
    }
}
