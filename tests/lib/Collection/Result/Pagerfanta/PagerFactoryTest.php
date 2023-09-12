<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result\Pagerfanta;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory;
use Netgen\Layouts\Collection\Result\ResultBuilderInterface;
use Netgen\Layouts\Collection\Result\ResultSet;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class PagerFactoryTest extends TestCase
{
    private MockObject $resultBuilderMock;

    private PagerFactory $pagerFactory;

    protected function setUp(): void
    {
        $this->resultBuilderMock = $this->createMock(ResultBuilderInterface::class);

        $this->resultBuilderMock
            ->method('build')
            ->willReturn(ResultSet::fromArray(['totalCount' => 1000]));

        $this->pagerFactory = new PagerFactory($this->resultBuilderMock, 200);
    }

    /**
     * @covers \Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     * @covers \Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory::getPager
     *
     * @dataProvider getPagerDataProvider
     */
    public function testGetPager(int $startPage, int $currentPage): void
    {
        $pager = $this->pagerFactory->getPager(Collection::fromArray(['offset' => 0, 'limit' => 5]), $startPage);

        self::assertTrue($pager->getNormalizeOutOfRangePages());
        self::assertSame(5, $pager->getMaxPerPage());
        self::assertSame($currentPage, $pager->getCurrentPage());
        self::assertSame(200, $pager->getNbPages());
    }

    public static function getPagerDataProvider(): iterable
    {
        return [
            [-5, 1],
            [-1, 1],
            [0, 1],
            [1, 1],
            [2, 2],
            [5, 5],
        ];
    }

    /**
     * @covers \Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     * @covers \Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory::getPager
     *
     * @dataProvider getMaxPagesPagerDataProvider
     */
    public function testGetPagerWithMaxPages(int $maxPages, int $currentPage, int $nbPages): void
    {
        $pager = $this->pagerFactory->getPager(Collection::fromArray(['offset' => 0, 'limit' => 5]), 2, $maxPages);

        self::assertTrue($pager->getNormalizeOutOfRangePages());
        self::assertSame(5, $pager->getMaxPerPage());
        self::assertSame($currentPage, $pager->getCurrentPage());
        self::assertSame($nbPages, $pager->getNbPages());
    }

    public static function getMaxPagesPagerDataProvider(): iterable
    {
        return [
            [-2, 2, 200],
            [-1, 2, 200],
            [0, 2, 200],
            [1, 1, 1],
            [2, 2, 2],
            [3, 2, 3],
            [4, 2, 4],
            [5, 2, 5],
            [200, 2, 200],
            [250, 2, 200],
        ];
    }

    /**
     * @covers \Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     * @covers \Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory::getPager
     *
     * @dataProvider getPagerWithCollectionLimitDataProvider
     */
    public function testGetPagerWithCollectionLimit(int $limit, ?int $maxPages, int $maxPerPage, int $nbPages): void
    {
        $pager = $this->pagerFactory->getPager(Collection::fromArray(['offset' => 0, 'limit' => $limit]), 2, $maxPages);

        self::assertTrue($pager->getNormalizeOutOfRangePages());
        self::assertSame($maxPerPage, $pager->getMaxPerPage());
        self::assertSame(2, $pager->getCurrentPage());
        self::assertSame($nbPages, $pager->getNbPages());
    }

    public static function getPagerWithCollectionLimitDataProvider(): iterable
    {
        return [
            [100, null, 100, 10],
            [100, 3, 100, 3],
            [100, 10, 100, 10],
            [199, null, 199, 6],
            [199, 3, 199, 3],
            [199, 10, 199, 6],
            [200, null, 200, 5],
            [200, 3, 200, 3],
            [200, 10, 200, 5],
            [201, null, 200, 5],
            [201, 3, 200, 3],
            [201, 10, 200, 5],
            [500, null, 200, 5],
            [500, 3, 200, 3],
            [500, 10, 200, 5],
        ];
    }
}
