<?php

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

    public function setUp()
    {
        $this->resultBuilderMock = $this->createMock(ResultBuilderInterface::class);

        $this->resultBuilderMock->expects($this->any())
            ->method('build')
            ->will($this->returnValue(new ResultSet(array('totalCount' => 1000))));

        $this->pagerFactory = new PagerFactory($this->resultBuilderMock, 200);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     */
    public function testGetPager()
    {
        $pager = $this->pagerFactory->getPager(new Collection(array('offset' => 0, 'limit' => 5)), 2);

        $this->assertInstanceOf(Pagerfanta::class, $pager);
        $this->assertTrue($pager->getNormalizeOutOfRangePages());
        $this->assertEquals(5, $pager->getMaxPerPage());
        $this->assertEquals(2, $pager->getCurrentPage());
        $this->assertEquals(200, $pager->getNbPages());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     */
    public function testGetPagerWithMaxPages()
    {
        $pager = $this->pagerFactory->getPager(new Collection(array('offset' => 0, 'limit' => 5)), 2, 5);

        $this->assertInstanceOf(Pagerfanta::class, $pager);
        $this->assertTrue($pager->getNormalizeOutOfRangePages());
        $this->assertEquals(5, $pager->getMaxPerPage());
        $this->assertEquals(2, $pager->getCurrentPage());
        $this->assertEquals(5, $pager->getNbPages());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     */
    public function testGetPagerWithMaxPagesLargerThanTotalCount()
    {
        $pager = $this->pagerFactory->getPager(new Collection(array('offset' => 0, 'limit' => 5)), 2, 250);

        $this->assertInstanceOf(Pagerfanta::class, $pager);
        $this->assertTrue($pager->getNormalizeOutOfRangePages());
        $this->assertEquals(5, $pager->getMaxPerPage());
        $this->assertEquals(2, $pager->getCurrentPage());
        $this->assertEquals(200, $pager->getNbPages());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     */
    public function testGetPagerWithLimitLargerThanMaxLimit()
    {
        $pager = $this->pagerFactory->getPager(new Collection(array('offset' => 0, 'limit' => 500)), 2);

        $this->assertInstanceOf(Pagerfanta::class, $pager);
        $this->assertTrue($pager->getNormalizeOutOfRangePages());
        $this->assertEquals(200, $pager->getMaxPerPage());
        $this->assertEquals(2, $pager->getCurrentPage());
        $this->assertEquals(5, $pager->getNbPages());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     */
    public function testGetPagerWithLimitLargerThanMaxLimitAndMaxPages()
    {
        $pager = $this->pagerFactory->getPager(new Collection(array('offset' => 0, 'limit' => 500)), 2, 3);

        $this->assertInstanceOf(Pagerfanta::class, $pager);
        $this->assertTrue($pager->getNormalizeOutOfRangePages());
        $this->assertEquals(200, $pager->getMaxPerPage());
        $this->assertEquals(2, $pager->getCurrentPage());
        $this->assertEquals(3, $pager->getNbPages());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::buildPager
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory::getMaxPerPage
     */
    public function testGetPagerWithLimitLargerThanMaxLimitAndMaxPagesLargerThanTotalCount()
    {
        $pager = $this->pagerFactory->getPager(new Collection(array('offset' => 0, 'limit' => 500)), 2, 10);

        $this->assertInstanceOf(Pagerfanta::class, $pager);
        $this->assertTrue($pager->getNormalizeOutOfRangePages());
        $this->assertEquals(200, $pager->getMaxPerPage());
        $this->assertEquals(2, $pager->getCurrentPage());
        $this->assertEquals(5, $pager->getNbPages());
    }
}
