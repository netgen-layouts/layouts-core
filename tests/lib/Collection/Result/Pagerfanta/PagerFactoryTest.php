<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result\Pagerfanta;

use Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory;
use Netgen\BlockManager\Collection\Result\ResultBuilderInterface;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;

final class PagerFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $resultBuilderMock;

    /**
     * @var \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory
     */
    private $pagerFactory;

    public function setUp(): void
    {
        $this->resultBuilderMock = $this->createMock(ResultBuilderInterface::class);

        $this->resultBuilderMock->expects($this->any())
            ->method('build')
            ->will($this->returnValue(new ResultSet(['totalCount' => 1000])));

        $this->pagerFactory = new PagerFactory($this->resultBuilderMock, 200);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getPager
     * @dataProvider getPagerProvider
     */
    public function testGetPager(int $startPage, int $currentPage): void
    {
        $pager = $this->pagerFactory->getPager(new Collection(['offset' => 0, 'limit' => 5]), $startPage);

        $this->assertInstanceOf(Pagerfanta::class, $pager);
        $this->assertTrue($pager->getNormalizeOutOfRangePages());
        $this->assertSame(5, $pager->getMaxPerPage());
        $this->assertSame($currentPage, $pager->getCurrentPage());
        $this->assertSame(200, $pager->getNbPages());
    }

    public function getPagerProvider(): array
    {
        return [
            [-5, 1],
            [-1, 1],
            [0, 1],
            [1, 1],
            [5, 5],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getPager
     */
    public function testGetPagerWithMaxPages(): void
    {
        $pager = $this->pagerFactory->getPager(new Collection(['offset' => 0, 'limit' => 5]), 2, 5);

        $this->assertInstanceOf(Pagerfanta::class, $pager);
        $this->assertTrue($pager->getNormalizeOutOfRangePages());
        $this->assertSame(5, $pager->getMaxPerPage());
        $this->assertSame(2, $pager->getCurrentPage());
        $this->assertSame(5, $pager->getNbPages());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getPager
     */
    public function testGetPagerWithMaxPagesLargerThanTotalCount(): void
    {
        $pager = $this->pagerFactory->getPager(new Collection(['offset' => 0, 'limit' => 5]), 2, 250);

        $this->assertInstanceOf(Pagerfanta::class, $pager);
        $this->assertTrue($pager->getNormalizeOutOfRangePages());
        $this->assertSame(5, $pager->getMaxPerPage());
        $this->assertSame(2, $pager->getCurrentPage());
        $this->assertSame(200, $pager->getNbPages());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getPager
     */
    public function testGetPagerWithLimitLargerThanMaxLimit(): void
    {
        $pager = $this->pagerFactory->getPager(new Collection(['offset' => 0, 'limit' => 500]), 2);

        $this->assertInstanceOf(Pagerfanta::class, $pager);
        $this->assertTrue($pager->getNormalizeOutOfRangePages());
        $this->assertSame(200, $pager->getMaxPerPage());
        $this->assertSame(2, $pager->getCurrentPage());
        $this->assertSame(5, $pager->getNbPages());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getPager
     */
    public function testGetPagerWithLimitLargerThanMaxLimitAndMaxPages(): void
    {
        $pager = $this->pagerFactory->getPager(new Collection(['offset' => 0, 'limit' => 500]), 2, 3);

        $this->assertInstanceOf(Pagerfanta::class, $pager);
        $this->assertTrue($pager->getNormalizeOutOfRangePages());
        $this->assertSame(200, $pager->getMaxPerPage());
        $this->assertSame(2, $pager->getCurrentPage());
        $this->assertSame(3, $pager->getNbPages());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getPager
     */
    public function testGetPagerWithLimitLargerThanMaxLimitAndMaxPagesLargerThanTotalCount(): void
    {
        $pager = $this->pagerFactory->getPager(new Collection(['offset' => 0, 'limit' => 500]), 2, 10);

        $this->assertInstanceOf(Pagerfanta::class, $pager);
        $this->assertTrue($pager->getNormalizeOutOfRangePages());
        $this->assertSame(200, $pager->getMaxPerPage());
        $this->assertSame(2, $pager->getCurrentPage());
        $this->assertSame(5, $pager->getNbPages());
    }
}
