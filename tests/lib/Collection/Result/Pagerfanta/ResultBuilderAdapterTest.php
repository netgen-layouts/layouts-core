<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result\Pagerfanta;

use Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter;
use Netgen\BlockManager\Collection\Result\ResultBuilderInterface;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use PHPUnit\Framework\TestCase;

final class ResultBuilderAdapterTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultBuilderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $resultBuilderMock;

    public function setUp(): void
    {
        $this->resultBuilderMock = $this->createMock(ResultBuilderInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::getNbResults
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::setTotalCount
     */
    public function testGetNbResults(): void
    {
        $collection = new Collection();
        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                $this->identicalTo($collection),
                $this->identicalTo(0),
                $this->identicalTo(0),
                $this->identicalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 3])));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, $collection);

        $this->assertSame(3, $adapter->getNbResults());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::getNbResults
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::setTotalCount
     */
    public function testGetNbResultsWithMaxTotalCount(): void
    {
        $collection = new Collection();
        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                $this->identicalTo($collection),
                $this->identicalTo(0),
                $this->identicalTo(0),
                $this->identicalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 50])));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, $collection, 0, 10);

        $this->assertSame(10, $adapter->getNbResults());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::getNbResults
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::setTotalCount
     */
    public function testGetNbResultsWithStartingOffset(): void
    {
        $collection = new Collection();
        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                $this->identicalTo($collection),
                $this->identicalTo(0),
                $this->identicalTo(0),
                $this->identicalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 6])));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, $collection, 3);

        $this->assertSame(3, $adapter->getNbResults());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::getNbResults
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::setTotalCount
     */
    public function testGetNbResultsWithStartingOffsetAndMaxTotalCount(): void
    {
        $collection = new Collection();
        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                $this->identicalTo($collection),
                $this->identicalTo(0),
                $this->identicalTo(0),
                $this->identicalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 10])));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, $collection, 3, 5);

        $this->assertSame(5, $adapter->getNbResults());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::getSlice
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::setTotalCount
     */
    public function testGetSlice(): void
    {
        $collection = new Collection();
        $resultSet = new ResultSet(['results' => [1, 2, 3], 'totalCount' => 3]);

        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                $this->identicalTo($collection),
                $this->identicalTo(0),
                $this->identicalTo(10),
                $this->identicalTo(0)
            )
            ->will($this->returnValue($resultSet));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, $collection);

        $this->assertSame($resultSet, $adapter->getSlice(0, 10));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::getSlice
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::setTotalCount
     */
    public function testGetSliceWithStartingOffset(): void
    {
        $collection = new Collection();
        $resultSet = new ResultSet(['results' => [1, 2, 3], 'totalCount' => 3]);

        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                $this->identicalTo($collection),
                $this->identicalTo(3),
                $this->identicalTo(10),
                $this->identicalTo(0)
            )
            ->will($this->returnValue($resultSet));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, $collection, 3);

        $this->assertSame($resultSet, $adapter->getSlice(0, 10));
    }
}
