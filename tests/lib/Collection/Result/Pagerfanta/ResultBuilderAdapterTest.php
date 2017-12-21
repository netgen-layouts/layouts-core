<?php

namespace Netgen\BlockManager\Tests\Collection\Result\Pagerfanta;

use Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter;
use Netgen\BlockManager\Collection\Result\ResultBuilderInterface;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use PHPUnit\Framework\TestCase;

class ResultBuilderAdapterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $resultBuilderMock;

    public function setUp()
    {
        $this->resultBuilderMock = $this->createMock(ResultBuilderInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::getNbResults
     */
    public function testGetNbResults()
    {
        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                $this->equalTo(new Collection()),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(array('totalCount' => 3))));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, new Collection());

        $this->assertEquals(3, $adapter->getNbResults());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::getNbResults
     */
    public function testGetNbResultsWithMaxTotalCount()
    {
        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                $this->equalTo(new Collection()),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(array('totalCount' => 50))));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, new Collection(), 0, 10);

        $this->assertEquals(10, $adapter->getNbResults());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::getNbResults
     */
    public function testGetNbResultsWithStartingOffset()
    {
        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                $this->equalTo(new Collection()),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(array('totalCount' => 6))));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, new Collection(), 3);

        $this->assertEquals(3, $adapter->getNbResults());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::getNbResults
     */
    public function testGetNbResultsWithStartingOffsetAndMaxTotalCount()
    {
        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                $this->equalTo(new Collection()),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(array('totalCount' => 10))));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, new Collection(), 3, 5);

        $this->assertEquals(5, $adapter->getNbResults());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter::getSlice
     */
    public function testGetSlice()
    {
        $resultSet = new ResultSet(array('results' => array(1, 2, 3)));

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
     */
    public function testGetSliceWithStartingOffset()
    {
        $resultSet = new ResultSet(array('results' => array(1, 2, 3)));

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
