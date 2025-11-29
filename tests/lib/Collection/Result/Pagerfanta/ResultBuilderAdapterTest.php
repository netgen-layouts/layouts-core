<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result\Pagerfanta;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\Collection\Result\Pagerfanta\ResultBuilderAdapter;
use Netgen\Layouts\Collection\Result\ResultBuilderInterface;
use Netgen\Layouts\Collection\Result\ResultSet;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ResultBuilderAdapter::class)]
final class ResultBuilderAdapterTest extends TestCase
{
    private MockObject&ResultBuilderInterface $resultBuilderMock;

    protected function setUp(): void
    {
        $this->resultBuilderMock = $this->createMock(ResultBuilderInterface::class);
    }

    public function testGetNbResults(): void
    {
        $collection = new Collection();
        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(0),
                self::identicalTo(0),
                self::identicalTo(0),
            )
            ->willReturn(ResultSet::fromArray(['totalCount' => 3]));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, $collection);

        self::assertSame(3, $adapter->getNbResults());
    }

    public function testGetNbResultsWithMaxTotalCount(): void
    {
        $collection = new Collection();
        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(0),
                self::identicalTo(0),
                self::identicalTo(0),
            )
            ->willReturn(ResultSet::fromArray(['totalCount' => 50]));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, $collection, 0, 10);

        self::assertSame(10, $adapter->getNbResults());
    }

    public function testGetNbResultsWithStartingOffset(): void
    {
        $collection = new Collection();
        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(0),
                self::identicalTo(0),
                self::identicalTo(0),
            )
            ->willReturn(ResultSet::fromArray(['totalCount' => 6]));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, $collection, 3);

        self::assertSame(3, $adapter->getNbResults());
    }

    public function testGetNbResultsWithStartingOffsetAndMaxTotalCount(): void
    {
        $collection = new Collection();
        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(0),
                self::identicalTo(0),
                self::identicalTo(0),
            )
            ->willReturn(ResultSet::fromArray(['totalCount' => 10]));

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, $collection, 3, 5);

        self::assertSame(5, $adapter->getNbResults());
    }

    public function testGetSlice(): void
    {
        $collection = new Collection();
        $resultSet = ResultSet::fromArray(['results' => [1, 2, 3], 'totalCount' => 3]);

        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(0),
                self::identicalTo(10),
                self::identicalTo(0),
            )
            ->willReturn($resultSet);

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, $collection);

        self::assertSame($resultSet, $adapter->getSlice(0, 10));
    }

    public function testGetSliceWithStartingOffset(): void
    {
        $collection = new Collection();
        $resultSet = ResultSet::fromArray(['results' => [1, 2, 3], 'totalCount' => 3]);

        $this->resultBuilderMock->expects($this->once())
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(3),
                self::identicalTo(10),
                self::identicalTo(0),
            )
            ->willReturn($resultSet);

        $adapter = new ResultBuilderAdapter($this->resultBuilderMock, $collection, 3);

        self::assertSame($resultSet, $adapter->getSlice(0, 10));
    }
}
