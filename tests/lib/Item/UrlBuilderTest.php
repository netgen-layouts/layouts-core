<?php

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\UrlBuilder;
use Netgen\BlockManager\Tests\Item\Stubs\ValueUrlBuilder;
use PHPUnit\Framework\TestCase;
use stdClass;

final class UrlBuilderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\UrlBuilderInterface
     */
    private $urlBuilder;

    public function setUp()
    {
        $this->urlBuilder = new UrlBuilder(
            array('value' => new ValueUrlBuilder())
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\UrlBuilder::__construct
     * @expectedException \Netgen\BlockManager\Exception\InvalidInterfaceException
     * @expectedExceptionMessage Value URL builder "stdClass" needs to implement "Netgen\BlockManager\Item\ValueUrlBuilderInterface" interface.
     */
    public function testConstructorThrowsInvalidInterfaceExceptionWithWrongInterface()
    {
        new UrlBuilder(array(new stdClass()));
    }

    /**
     * @covers \Netgen\BlockManager\Item\UrlBuilder::__construct
     * @covers \Netgen\BlockManager\Item\UrlBuilder::getUrl
     */
    public function testGetUrl()
    {
        $this->assertEquals(
            '/item-url',
            $this->urlBuilder->getUrl(
                new Item(array('valueType' => 'value'))
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\UrlBuilder::getUrl
     * @expectedException \Netgen\BlockManager\Exception\Item\ValueException
     * @expectedExceptionMessage Value URL builder for "unknown" value type does not exist.
     */
    public function testGetUrlWithNoUrlBuilder()
    {
        $this->urlBuilder->getUrl(
            new Item(array('valueType' => 'unknown'))
        );
    }
}
