<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\Result\QueryRunner;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\CmsItemBuilderInterface;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\Tests\Collection\Result\Stubs\Value;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;
use function iterator_to_array;

final class QueryRunnerTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Item\CmsItemBuilderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $cmsItemBuilderMock;

    protected function setUp(): void
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
     * @covers \Netgen\Layouts\Collection\Result\QueryRunner::__construct
     * @covers \Netgen\Layouts\Collection\Result\QueryRunner::count
     * @covers \Netgen\Layouts\Collection\Result\QueryRunner::runQuery
     */
    public function testRunner(): void
    {
        $queryType = new QueryType('query', [new Value(40), new Value(41), new Value(42)]);
        $query = Query::fromArray(['queryType' => $queryType]);

        $queryRunner = new QueryRunner($this->cmsItemBuilderMock);

        /** @var \Netgen\Layouts\Item\CmsItemInterface[] $items */
        $items = iterator_to_array($queryRunner->runQuery($query));
        self::assertContainsOnlyInstancesOf(CmsItemInterface::class, $items);

        foreach ($items as $item) {
            self::assertTrue($item->isVisible());
        }

        self::assertSame(40, $items[0]->getValue()->getValue());
        self::assertSame(41, $items[1]->getValue()->getValue());
        self::assertSame(42, $items[2]->getValue()->getValue());

        self::assertSame(3, $queryRunner->count($query));
    }
}
