<?php

namespace Netgen\BlockManager\Tests\HttpCache\Layout\Strategy\Ban;

use FOS\HttpCache\CacheInvalidator;
use Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\IdProviderInterface;
use Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\Invalidator;
use PHPUnit\Framework\TestCase;

class InvalidatorTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fosInvalidatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $idProviderMock;

    /**
     * @var \Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\Invalidator
     */
    protected $invalidator;

    public function setUp()
    {
        $this->fosInvalidatorMock = $this->createMock(CacheInvalidator::class);
        $this->idProviderMock = $this->createMock(IdProviderInterface::class);

        $this->invalidator = new Invalidator(
            $this->fosInvalidatorMock,
            $this->idProviderMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\Invalidator::__construct
     * @covers \Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\Invalidator::invalidate
     */
    public function testInvalidate()
    {
        $this->idProviderMock
            ->expects($this->at(0))
            ->method('provideIds')
            ->with($this->equalTo(24))
            ->will($this->returnValue(array(24, 25, 26)));

        $this->idProviderMock
            ->expects($this->at(1))
            ->method('provideIds')
            ->with($this->equalTo(42))
            ->will($this->returnValue(array(42)));

        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with(
                $this->equalTo(
                    array(
                        'X-Layout-Id' => '^(24|25|26|42)$',
                    )
                )
            );

        $this->invalidator->invalidate(array(24, 42));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\Invalidator::invalidate
     */
    public function testInvalidateWithEmptyLayoutIds()
    {
        $this->idProviderMock
            ->expects($this->never())
            ->method('provideIds');

        $this->fosInvalidatorMock
            ->expects($this->never())
            ->method('invalidate');

        $this->invalidator->invalidate(array());
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\Invalidator::invalidateAll
     */
    public function testInvalidateAll()
    {
        $this->idProviderMock
            ->expects($this->never())
            ->method('provideIds');

        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with(
                $this->equalTo(
                    array(
                        'X-Layout-Id' => '.*',
                    )
                )
            );

        $this->invalidator->invalidateAll();
    }
}
