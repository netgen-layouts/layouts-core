<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\UrlBuilderInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\ItemExtension;
use PHPUnit\Framework\TestCase;
use Twig_SimpleFunction;

class ItemExtensionTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemLoaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilderMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\ItemExtension
     */
    protected $extension;

    public function setUp()
    {
        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);
        $this->urlBuilderMock = $this->createMock(UrlBuilderInterface::class);

        $this->extension = new ItemExtension(
            $this->itemLoaderMock,
            $this->urlBuilderMock
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\ItemExtension::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\ItemExtension::getName
     */
    public function testGetName()
    {
        $this->assertEquals(get_class($this->extension), $this->extension->getName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\ItemExtension::getFunctions
     */
    public function testGetFunctions()
    {
        $this->assertNotEmpty($this->extension->getFunctions());

        foreach ($this->extension->getFunctions() as $function) {
            $this->assertInstanceOf(Twig_SimpleFunction::class, $function);
        }
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\ItemExtension::getItemPath
     */
    public function testGetItemPath()
    {
        $this->itemLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($this->equalTo(42), $this->equalTo('value'))
            ->will($this->returnValue(new Item()));

        $this->urlBuilderMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo(new Item()))
            ->will($this->returnValue('/item/path'));

        $itemPath = $this->extension->getItemPath(42, 'value');

        $this->assertEquals('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\ItemExtension::getItemPath
     */
    public function testGetItemPathWithUri()
    {
        $this->itemLoaderMock
            ->expects($this->once())
            ->method('load')
            ->with($this->equalTo(42), $this->equalTo('value'))
            ->will($this->returnValue(new Item()));

        $this->urlBuilderMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo(new Item()))
            ->will($this->returnValue('/item/path'));

        $itemPath = $this->extension->getItemPath('value://42');

        $this->assertEquals('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\ItemExtension::getItemPath
     */
    public function testGetItemPathWithItem()
    {
        $this->itemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlBuilderMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo(new Item()))
            ->will($this->returnValue('/item/path'));

        $itemPath = $this->extension->getItemPath(new Item());

        $this->assertEquals('/item/path', $itemPath);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\ItemExtension::getItemPath
     */
    public function testGetItemPathWithInvalidValue()
    {
        $this->itemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlBuilderMock
            ->expects($this->never())
            ->method('getUrl');

        $this->assertEquals('', $this->extension->getItemPath('value'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\ItemExtension::getItemPath
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\ItemExtension::setDebug
     * @expectedException \Netgen\BlockManager\Exception\InvalidItemException
     * @expectedExceptionMessage Item "value" is not valid.
     */
    public function testGetItemPathThrowsInvalidItemExceptionInDebugModeWithInvalidValue()
    {
        $this->extension->setDebug(true);

        $this->itemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlBuilderMock
            ->expects($this->never())
            ->method('getUrl');

        $this->extension->getItemPath('value');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\ItemExtension::getItemPath
     */
    public function testGetItemPathWithUnsupportedValue()
    {
        $this->itemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlBuilderMock
            ->expects($this->never())
            ->method('getUrl');

        $this->assertEquals('', $this->extension->getItemPath(42));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\ItemExtension::getItemPath
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\ItemExtension::setDebug
     * @expectedException \Netgen\BlockManager\Exception\InvalidItemException
     * @expectedExceptionMessage Item could not be loaded.
     */
    public function testGetItemPathThrowsInvalidItemExceptionInDebugModeWithUnsupportedValue()
    {
        $this->extension->setDebug(true);

        $this->itemLoaderMock
            ->expects($this->never())
            ->method('load');

        $this->urlBuilderMock
            ->expects($this->never())
            ->method('getUrl');

        $this->extension->getItemPath(42);
    }
}
