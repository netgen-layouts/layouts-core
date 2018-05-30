<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterType\ItemLink;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\NullItem;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use PHPUnit\Framework\TestCase;

final class RemoteIdConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $itemLoaderMock;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter
     */
    private $remoteIdConverter;

    public function setUp()
    {
        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);

        $this->remoteIdConverter = new RemoteIdConverter($this->itemLoaderMock);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertToRemoteId
     */
    public function testCovertToRemoteId()
    {
        $this->itemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->with($this->equalTo('42'), $this->equalTo('my_value_type'))
            ->will(
                $this->returnValue(
                    new Item(
                        [
                            'value' => 42,
                            'remoteId' => 'abc',
                        ]
                    )
                )
            );

        $this->assertEquals('my-value-type://abc', $this->remoteIdConverter->convertToRemoteId('my-value-type://42'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertToRemoteId
     */
    public function testCovertToRemoteIdWithInvalidLink()
    {
        $this->assertEquals('null://0', $this->remoteIdConverter->convertToRemoteId('invalid'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertToRemoteId
     */
    public function testConvertToRemoteIdWithNullItem()
    {
        $this->itemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->with($this->equalTo(42), $this->equalTo('my_value_type'))
            ->will($this->returnValue(new NullItem('my_value_type')));

        $this->assertEquals('null://0', $this->remoteIdConverter->convertToRemoteId('my-value-type://42'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertFromRemoteId
     */
    public function testConvertFromRemoteId()
    {
        $this->itemLoaderMock
            ->expects($this->any())
            ->method('loadByRemoteId')
            ->with($this->equalTo('abc'), $this->equalTo('my_value_type'))
            ->will(
                $this->returnValue(
                    new Item(
                        [
                            'value' => 42,
                            'remoteId' => 'abc',
                        ]
                    )
                )
            );

        $this->assertEquals('my-value-type://42', $this->remoteIdConverter->convertFromRemoteId('my-value-type://abc'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertFromRemoteId
     */
    public function testConvertFromRemoteIdWithInvalidLink()
    {
        $this->assertEquals('null://0', $this->remoteIdConverter->convertFromRemoteId('invalid'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertFromRemoteId
     */
    public function testConvertFromRemoteIdWithNullItem()
    {
        $this->itemLoaderMock
            ->expects($this->any())
            ->method('loadByRemoteId')
            ->with($this->equalTo('abc'), $this->equalTo('my_value_type'))
            ->will($this->returnValue(new NullItem('my_value_type')));

        $this->assertEquals('null://0', $this->remoteIdConverter->convertFromRemoteId('my-value-type://abc'));
    }
}
