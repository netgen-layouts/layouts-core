<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\PageExtension;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Exception\NotFoundException;
use Twig_SimpleFunction;
use PHPUnit\Framework\TestCase;

class PageExtensionTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\PageExtension
     */
    protected $extension;

    public function setUp()
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->extension = new PageExtension(
            $this->layoutServiceMock
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\PageExtension::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\PageExtension::getName
     */
    public function testGetName()
    {
        self::assertEquals('ngbm_page', $this->extension->getName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\PageExtension::getFunctions
     */
    public function testGetFunctions()
    {
        self::assertNotEmpty($this->extension->getFunctions());

        foreach ($this->extension->getFunctions() as $function) {
            self::assertInstanceOf(Twig_SimpleFunction::class, $function);
        }
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\PageExtension::getLayoutName
     */
    public function testGetLayoutName()
    {
        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Layout(array('name' => 'Layout name'))));

        self::assertEquals(
            'Layout name',
            $this->extension->getLayoutName(42)
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\PageExtension::getLayoutName
     */
    public function testGetLayoutNameOnNonExistingLayout()
    {
        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException('layout', 42)));

        self::assertEquals(
            null,
            $this->extension->getLayoutName(42)
        );
    }
}
