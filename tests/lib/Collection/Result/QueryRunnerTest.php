<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\QueryRunner;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

final class QueryRunnerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $itemBuilderMock;

    public function setUp(): void
    {
        $this->itemBuilderMock = $this->createMock(ItemBuilderInterface::class);

        $this->itemBuilderMock
            ->expects($this->any())
            ->method('build')
            ->will(
                $this->returnCallback(
                    function ($value): ItemInterface {
                        return new Item(['value' => $value, 'isVisible' => true]);
                    }
                )
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
        $query = new Query(['queryType' => $queryType]);

        $queryRunner = new QueryRunner($this->itemBuilderMock);

        $items = iterator_to_array($queryRunner->runQuery($query));

        foreach ($items as $item) {
            $this->assertInstanceOf(Item::class, $item);
            $this->assertTrue($item->isVisible());
        }

        $this->assertSame(40, $items[0]->getValue());
        $this->assertSame(41, $items[1]->getValue());
        $this->assertSame(42, $items[2]->getValue());

        $this->assertSame(3, $queryRunner->count($query));
    }
}
