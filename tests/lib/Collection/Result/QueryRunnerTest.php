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
     * @var \PHPUnit\Framework\MockObject\MockObject
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
                        return new Item(array('value' => $value, 'isVisible' => true));
                    }
                )
            );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\QueryRunner::__construct
     * @covers \Netgen\BlockManager\Collection\Result\QueryRunner::__invoke
     * @covers \Netgen\BlockManager\Collection\Result\QueryRunner::count
     */
    public function testRunner()
    {
        $expectedItems = array(
            new Item(array('value' => 40, 'isVisible' => true)),
            new Item(array('value' => 41, 'isVisible' => true)),
            new Item(array('value' => 42, 'isVisible' => true)),
        );

        $queryType = new QueryType('query', array(40, 41, 42));
        $query = new Query(array('queryType' => $queryType));

        $queryRunner = new QueryRunner($this->itemBuilderMock);

        $this->assertEquals($expectedItems, iterator_to_array($queryRunner($query)));
        $this->assertEquals(3, $queryRunner->count($query));
    }
}
