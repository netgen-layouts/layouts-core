<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\TwigBlockDefinition;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Twig_SimpleFunction;
use Twig_TokenParser;
use Twig_Template;
use Exception;
use PHPUnit\Framework\TestCase;

class RenderingExtensionTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $globalVariableMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewRendererMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fragmentHandlerMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension
     */
    protected $extension;

    public function setUp()
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);
        $this->globalVariableMock = $this->createMock(GlobalVariable::class);
        $this->viewRendererMock = $this->createMock(RendererInterface::class);
        $this->fragmentHandlerMock = $this->createMock(FragmentHandler::class);

        $this->extension = new RenderingExtension(
            $this->layoutServiceMock,
            $this->globalVariableMock,
            $this->viewRendererMock,
            $this->fragmentHandlerMock,
            'ngbm_block:viewBlockById'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::getName
     */
    public function testGetName()
    {
        $this->assertEquals(get_class($this->extension), $this->extension->getName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::getGlobals
     */
    public function testGetGlobals()
    {
        $this->assertEquals(
            array(
                'ngbm' => $this->globalVariableMock,
            ),
            $this->extension->getGlobals()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::getFunctions
     */
    public function testGetFunctions()
    {
        $this->assertNotEmpty($this->extension->getFunctions());

        foreach ($this->extension->getFunctions() as $function) {
            $this->assertInstanceOf(Twig_SimpleFunction::class, $function);
        }
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::getTokenParsers
     */
    public function testGetTokenParsers()
    {
        $this->assertNotEmpty($this->extension->getTokenParsers());

        foreach ($this->extension->getTokenParsers() as $tokenParser) {
            $this->assertInstanceOf(Twig_TokenParser::class, $tokenParser);
        }
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::displayBlock
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::logBlockError
     */
    public function testDisplayBlockReturnsEmptyStringOnException()
    {
        $twigTemplateMock = $this->createMock(Twig_Template::class);

        $block = new Block(array('blockDefinition' => new BlockDefinition('block')));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo($block),
                $this->equalTo(array()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT)
            )
            ->will($this->throwException(new Exception()));

        $this->assertEquals(
            '',
            $this->extension->displayBlock(
                $block,
                ViewInterface::CONTEXT_DEFAULT,
                $twigTemplateMock,
                array(),
                array()
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::displayBlock
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::logBlockError
     * @expectedException \Netgen\Bundle\BlockManagerBundle\Exception\RenderingFailedException
     */
    public function testDisplayBlockThrowsExceptionInDebug()
    {
        $twigTemplateMock = $this->createMock(Twig_Template::class);

        $this->extension->setDebug(true);
        $block = new Block(array('blockDefinition' => new BlockDefinition('block')));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo($block),
                $this->equalTo(array()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT)
            )
            ->will($this->throwException(new Exception()));

        $this->extension->displayBlock(
            $block,
            ViewInterface::CONTEXT_DEFAULT,
            $twigTemplateMock,
            array(),
            array()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderItem
     */
    public function testRenderItem()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Item()),
                $this->equalTo(array('viewType' => 'viewType', 'param' => 'value')),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT)
            )
            ->will($this->returnValue('rendered item'));

        $this->assertEquals(
            'rendered item',
            $this->extension->renderItem(
                array(),
                new Item(),
                'viewType',
                array('param' => 'value'),
                ViewInterface::CONTEXT_DEFAULT
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderItem
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::logItemError
     */
    public function testRenderItemReturnsEmptyStringOnException()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Item()),
                $this->equalTo(array('viewType' => 'viewType', 'param' => 'value')),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT)
            )
            ->will($this->throwException(new Exception()));

        $this->assertEquals(
            '',
            $this->extension->renderItem(
                array(),
                new Item(),
                'viewType',
                array('param' => 'value'),
                ViewInterface::CONTEXT_DEFAULT
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderItem
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::logItemError
     * @expectedException \Netgen\Bundle\BlockManagerBundle\Exception\RenderingFailedException
     */
    public function testRenderItemThrowsExceptionInDebug()
    {
        $this->extension->setDebug(true);

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Item()),
                $this->equalTo(array('viewType' => 'viewType', 'param' => 'value')),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT)
            )
            ->will($this->throwException(new Exception()));

        $this->extension->renderItem(
            array(),
            new Item(),
            'viewType',
            array('param' => 'value'),
            ViewInterface::CONTEXT_DEFAULT
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderValueObject
     */
    public function testRenderValueObject()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Condition()),
                $this->equalTo(array('param' => 'value')),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT)
            )
            ->will($this->returnValue('rendered value'));

        $this->assertEquals(
            'rendered value',
            $this->extension->renderValueObject(
                array(),
                new Condition(),
                array('param' => 'value'),
                ViewInterface::CONTEXT_DEFAULT
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderValueObject
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::logValueObjectError
     */
    public function testRenderValueObjectReturnsEmptyStringOnException()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Condition()),
                $this->equalTo(array('param' => 'value')),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT)
            )
            ->will($this->throwException(new Exception()));

        $this->assertEquals(
            '',
            $this->extension->renderValueObject(
                array(),
                new Condition(),
                array('param' => 'value'),
                ViewInterface::CONTEXT_DEFAULT
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderValueObject
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::logValueObjectError
     * @expectedException \Netgen\Bundle\BlockManagerBundle\Exception\RenderingFailedException
     */
    public function testRenderValueObjectThrowsExceptionInDebug()
    {
        $this->extension->setDebug(true);

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Condition()),
                $this->equalTo(array('param' => 'value')),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT)
            )
            ->will($this->throwException(new Exception()));

        $this->extension->renderValueObject(
            array(),
            new Condition(),
            array('param' => 'value'),
            ViewInterface::CONTEXT_DEFAULT
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::displayZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::setDebug
     * @expectedException \Netgen\Bundle\BlockManagerBundle\Exception\RenderingFailedException
     */
    public function testDisplayZoneThrowsExceptionInDebug()
    {
        $twigTemplateMock = $this->createMock(Twig_Template::class);

        $twigTemplateMock
            ->expects($this->at(0))
            ->method('displayBlock')
            ->will($this->throwException(new Exception()));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject');

        $this->extension->setDebug(true);
        $this->extension->displayZone(
            new Zone(
                array(
                    'blocks' => array(
                        new Block(
                            array(
                                'blockDefinition' => new BlockDefinition('block_definition'),
                            )
                        ),
                        new Block(
                            array(
                                'blockDefinition' => new TwigBlockDefinition('twig_block'),
                                'parameters' => array(
                                    'block_name' => new ParameterValue(
                                        array(
                                            'value' => 'my_block',
                                        )
                                    ),
                                ),
                            )
                        ),
                        new Block(
                            array(
                                'blockDefinition' => new BlockDefinition('block_definition'),
                            )
                        ),
                    ),
                )
            ),
            ViewInterface::CONTEXT_DEFAULT,
            $twigTemplateMock,
            array(),
            array()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::displayZone
     */
    public function testDisplayZoneRendersBlocksOnException()
    {
        $twigTemplateMock = $this->createMock(Twig_Template::class);

        $twigTemplateMock
            ->expects($this->at(0))
            ->method('displayBlock')
            ->will($this->throwException(new Exception()));

        $this->viewRendererMock
            ->expects($this->at(0))
            ->method('renderValueObject')
            ->will($this->returnValue('rendered block 1'));

        $this->viewRendererMock
            ->expects($this->at(1))
            ->method('renderValueObject')
            ->will($this->returnValue('rendered block 2'));

        ob_start();

        $this->extension->displayZone(
            new Zone(
                array(
                    'blocks' => array(
                        new Block(
                            array(
                                'blockDefinition' => new BlockDefinition('block_definition'),
                            )
                        ),
                        new Block(
                            array(
                                'blockDefinition' => new TwigBlockDefinition('twig_block'),
                                'parameters' => array(
                                    'block_name' => new ParameterValue(
                                        array(
                                            'value' => 'my_block',
                                        )
                                    ),
                                ),
                            )
                        ),
                        new Block(
                            array(
                                'blockDefinition' => new BlockDefinition('block_definition'),
                            )
                        ),
                    ),
                )
            ),
            ViewInterface::CONTEXT_DEFAULT,
            $twigTemplateMock,
            array(),
            array()
        );

        $renderedTemplate = ob_get_clean();

        $this->assertEquals(
            'rendered block 1rendered block 2',
            $renderedTemplate
        );
    }
}
