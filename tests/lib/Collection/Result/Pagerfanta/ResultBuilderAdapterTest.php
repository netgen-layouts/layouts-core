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
        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                $this->equalTo(new Collection()),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 3])));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, new Collection());

        $this->assertEquals(3, $adapter->getNbResults());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::getNbResults
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::setTotalCount
     */
    public function testGetNbResultsWithMaxTotalCount(): void
    {
        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                $this->equalTo(new Collection()),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 50])));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, new Collection(), 0, 10);

        $this->assertEquals(10, $adapter->getNbResults());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::getNbResults
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::setTotalCount
     */
    public function testGetNbResultsWithStartingOffset(): void
    {
        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                $this->equalTo(new Collection()),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 6])));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, new Collection(), 3);

        $this->assertEquals(3, $adapter->getNbResults());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::getNbResults
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::setTotalCount
     */
    public function testGetNbResultsWithStartingOffsetAndMaxTotalCount(): void
    {
        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                $this->equalTo(new Collection()),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 10])));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, new Collection(), 3, 5);

        $this->assertEquals(5, $adapter->getNbResults());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::getSlice
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::setTotalCount
     */
    public function testGetSlice(): void
    {
        $resultSet = new ResultSet(['results' => [1, 2, 3]]);

        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                $this->equalTo(new Collection()),
                $this->equalTo(0),
                $this->equalTo(10),
                $this->equalTo(0)
            )
            ->will($this->returnValue($resultSet));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, new Collection());

        $this->assertEquals($resultSet, $adapter->getSlice(0, 10));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::getSlice
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::setTotalCount
     */
    public function testGetSliceWithStartingOffset(): void
    {
        $resultSet = new ResultSet(['results' => [1, 2, 3]]);

        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                $this->equalTo(new Collection()),
                $this->equalTo(3),
                $this->equalTo(10),
                $this->equalTo(0)
            )
            ->will($this->returnValue($resultSet));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, new Collection(), 3);

        $this->assertEquals($resultSet, $adapter->getSlice(0, 10));
    }
}
