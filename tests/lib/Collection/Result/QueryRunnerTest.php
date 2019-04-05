<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\Result\QueryRunner;
use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\CmsItemBuilderInterface;
use Netgen\BlockManager\Item\CmsItemInterface;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

final class QueryRunnerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\CmsItemBuilderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $cmsItemBuilderMock;

    public function setUp(): void
    {
        $this->cmsItemBuilderMock = $this->createMock(CmsItemBuilderInterface::class);

        $this->cmsItemBuilderMock
            ->expects(self::any())
            ->method('build')
            ->willReturnCallback(
                static function ($value): CmsItemInterface {
                    return CmsItem::fromArray(['value' => $value, 'isVisible' => true]);
                }
            );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\QueryRunner::__construct
     * @covers \Netgen\BlockManager\Collection\Result\QueryRunner::count
     * @covers \Netgen\BlockManager\Collection\Result\QueryRunner::runQuery
     */
    public function testRunner(): void
    {
        $queryType = new QueryType('query', [40, 41, 42]);
        $query = Query::fromArray(['queryType' => $queryType]);

        $queryRunner = new QueryRunner($this->cmsItemBuilderMock);

        $items = iterator_to_array($queryRunner->runQuery($query));
        self::assertContainsOnlyInstancesOf(CmsItemInterface::class, $items);

        foreach ($items as $item) {
            self::assertTrue($item->isVisible());
        }

        self::assertSame(40, $items[0]->getValue());
        self::assertSame(41, $items[1]->getValue());
        self::assertSame(42, $items[2]->getValue());

        self::assertSame(3, $queryRunner->count($query));
    }
}
