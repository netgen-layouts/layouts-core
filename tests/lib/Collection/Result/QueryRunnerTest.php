<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\QueryRunner;
use Netgen\BlockManager\Core\Values\Collection\Query;
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
            ->expects($this->any())
            ->method('build')
            ->will(
                $this->returnCallback(
                    function ($value): CmsItemInterface {
                        return CmsItem::fromArray(['value' => $value, 'isVisible' => true]);
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
        $query = Query::fromArray(['queryType' => $queryType]);

        $queryRunner = new QueryRunner($this->cmsItemBuilderMock);

        $items = iterator_to_array($queryRunner->runQuery($query));

        foreach ($items as $item) {
            $this->assertInstanceOf(CmsItemInterface::class, $item);
            $this->assertTrue($item->isVisible());
        }

        $this->assertSame(40, $items[0]->getValue());
        $this->assertSame(41, $items[1]->getValue());
        $this->assertSame(42, $items[2]->getValue());

        $this->assertSame(3, $queryRunner->count($query));
    }
}
