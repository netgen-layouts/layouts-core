<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\QueryRunner;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

final class QueryRunnerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $itemBuilderMock;

    public function setUp()
    {
        $this->itemBuilderMock = $this->createMock(ItemBuilderInterface::class);

        $this->itemBuilderMock
            ->expects($this->any())
            ->method('build')
            ->will(
                $this->returnCallback(
                    function ($value) {
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
    public function testRunner()
    {
        $expectedItems = [
            new Item(['value' => 40, 'isVisible' => true]),
            new Item(['value' => 41, 'isVisible' => true]),
            new Item(['value' => 42, 'isVisible' => true]),
        ];

        $queryType = new QueryType('query', [40, 41, 42]);
        $query = new Query(['queryType' => $queryType]);

        $queryRunner = new QueryRunner($this->itemBuilderMock);

        $this->assertEquals($expectedItems, iterator_to_array($queryRunner->runQuery($query)));
        $this->assertEquals(3, $queryRunner->count($query));
    }
}
