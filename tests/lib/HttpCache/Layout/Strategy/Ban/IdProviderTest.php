<?php

namespace Netgen\BlockManager\Tests\HttpCache\Layout\Strategy\Ban;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\IdProvider;
use PHPUnit\Framework\TestCase;

class IdProviderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutServiceMock;

    /**
     * @var \Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\IdProvider
     */
    protected $idProvider;

    public function setUp()
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->idProvider = new IdProvider($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\IdProvider::__construct
     * @covers \Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\IdProvider::provideIds
     */
    public function testProvideIds()
    {
        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->with($this->equalTo(42))
            ->will(
                $this->returnValue(
                    new Layout(
                        array(
                            'id' => 42,
                            'shared' => false,
                        )
                    )
                )
            );

        $providedIds = $this->idProvider->provideIds(42);

        $this->assertEquals(array(42), $providedIds);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\IdProvider::provideIds
     */
    public function testProvideIdsWithNonExistingLayout()
    {
        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->with($this->equalTo(42))
            ->will(
                $this->throwException(
                    new NotFoundException('layout', 42)
                )
            );

        $providedIds = $this->idProvider->provideIds(42);

        $this->assertEquals(array(42), $providedIds);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\IdProvider::provideIds
     */
    public function testProvideIdsWithSharedLayout()
    {
        $this->markTestIncomplete('Test for ID provider with shared layout is not implemented yet.');
    }
}
