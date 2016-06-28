<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\Block\BlockDefinition\Handler\TwigBlockHandler;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Twig_SimpleFunction;
use Twig_TokenParser;
use Twig_Template;
use Exception;
use PHPUnit\Framework\TestCase;

class RenderingExtensionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry
     */
    protected $blockDefinitionRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $globalHelperMock;

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
        $this->blockDefinitionRegistry = new BlockDefinitionRegistry();

        $this->blockDefinitionRegistry->addBlockDefinition(
            new BlockDefinition(
                'block_definition',
                new BlockDefinitionHandler(),
                new Configuration('block_definition', array(), array())
            )
        );

        $this->blockDefinitionRegistry->addBlockDefinition(
            new BlockDefinition(
                'twig_block',
                new TwigBlockHandler(),
                new Configuration('twig_block', array(), array())
            )
        );

        $this->globalHelperMock = $this->createMock(GlobalHelper::class);

        $this->viewRendererMock = $this->createMock(RendererInterface::class);

        $this->fragmentHandlerMock = $this->createMock(FragmentHandler::class);

        $this->extension = new RenderingExtension(
            $this->blockDefinitionRegistry,
            $this->globalHelperMock,
            $this->viewRendererMock,
            $this->fragmentHandlerMock
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::getName
     */
    public function testGetName()
    {
        self::assertEquals('ngbm_render', $this->extension->getName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::getGlobals
     */
    public function testGetGlobals()
    {
        self::assertEquals(
            array(
                'ngbm' => $this->globalHelperMock,
            ),
            $this->extension->getGlobals()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::getFunctions
     */
    public function testGetFunctions()
    {
        self::assertNotEmpty($this->extension->getFunctions());

        foreach ($this->extension->getFunctions() as $function) {
            self::assertInstanceOf(Twig_SimpleFunction::class, $function);
        }
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::getTokenParsers
     */
    public function testGetTokenParsers()
    {
        self::assertNotEmpty($this->extension->getTokenParsers());

        foreach ($this->extension->getTokenParsers() as $tokenParser) {
            self::assertInstanceOf(Twig_TokenParser::class, $tokenParser);
        }
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderBlock
     */
    public function testRenderBlock()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Block()),
                $this->equalTo(array('param' => 'value')),
                $this->equalTo(ViewInterface::CONTEXT_VIEW)
            )
            ->will($this->returnValue('rendered block'));

        self::assertEquals(
            'rendered block',
            $this->extension->renderBlock(
                new Block(),
                array('param' => 'value'),
                ViewInterface::CONTEXT_VIEW
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderBlock
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::logBlockError
     */
    public function testRenderBlockReturnsEmptyStringOnException()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Block()),
                $this->equalTo(array('param' => 'value')),
                $this->equalTo(ViewInterface::CONTEXT_VIEW)
            )
            ->will($this->throwException(new Exception()));

        self::assertEquals(
            '',
            $this->extension->renderBlock(
                new Block(),
                array('param' => 'value'),
                ViewInterface::CONTEXT_VIEW
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderBlock
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::logBlockError
     * @expectedException \Exception
     */
    public function testRenderBlockThrowsExceptionInDebug()
    {
        $this->extension->setDebug(true);

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Block()),
                $this->equalTo(array('param' => 'value')),
                $this->equalTo(ViewInterface::CONTEXT_VIEW)
            )
            ->will($this->throwException(new Exception()));

        $this->extension->renderBlock(
            new Block(),
            array('param' => 'value'),
            ViewInterface::CONTEXT_VIEW
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
                $this->equalTo(ViewInterface::CONTEXT_VIEW)
            )
            ->will($this->returnValue('rendered item'));

        self::assertEquals(
            'rendered item',
            $this->extension->renderItem(
                new Item(),
                'viewType',
                array('param' => 'value'),
                ViewInterface::CONTEXT_VIEW
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
                $this->equalTo(ViewInterface::CONTEXT_VIEW)
            )
            ->will($this->throwException(new Exception()));

        self::assertEquals(
            '',
            $this->extension->renderItem(
                new Item(),
                'viewType',
                array('param' => 'value'),
                ViewInterface::CONTEXT_VIEW
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderItem
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::logItemError
     * @expectedException \Exception
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
                $this->equalTo(ViewInterface::CONTEXT_VIEW)
            )
            ->will($this->throwException(new Exception()));

        $this->extension->renderItem(
            new Item(),
            'viewType',
            array('param' => 'value'),
            ViewInterface::CONTEXT_VIEW
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
                $this->equalTo(ViewInterface::CONTEXT_VIEW)
            )
            ->will($this->returnValue('rendered value'));

        self::assertEquals(
            'rendered value',
            $this->extension->renderValueObject(
                new Condition(),
                array('param' => 'value'),
                ViewInterface::CONTEXT_VIEW
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
                $this->equalTo(ViewInterface::CONTEXT_VIEW)
            )
            ->will($this->throwException(new Exception()));

        self::assertEquals(
            '',
            $this->extension->renderValueObject(
                new Condition(),
                array('param' => 'value'),
                ViewInterface::CONTEXT_VIEW
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderValueObject
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::logValueObjectError
     * @expectedException \Exception
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
                $this->equalTo(ViewInterface::CONTEXT_VIEW)
            )
            ->will($this->throwException(new Exception()));

        $this->extension->renderValueObject(
            new Condition(),
            array('param' => 'value'),
            ViewInterface::CONTEXT_VIEW
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::displayZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::setDebug
     * @expectedException \Exception
     */
    public function testDisplayZoneThrowsException()
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
                        new Block(array('definitionIdentifier' => 'block_definition')),
                        new Block(array('definitionIdentifier' => 'twig_block')),
                        new Block(array('definitionIdentifier' => 'block_definition')),
                    ),
                )
            ),
            ViewInterface::CONTEXT_VIEW,
            $twigTemplateMock,
            array(),
            array()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::displayZone
     */
    public function testDisplayZoneThrowsExceptionInDebugMode()
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
                        new Block(array('definitionIdentifier' => 'block_definition')),
                        new Block(array('definitionIdentifier' => 'twig_block')),
                        new Block(array('definitionIdentifier' => 'block_definition')),
                    ),
                )
            ),
            ViewInterface::CONTEXT_VIEW,
            $twigTemplateMock,
            array(),
            array()
        );

        $renderedTemplate = ob_get_contents();

        ob_get_clean();

        self::assertEquals(
            'rendered block 1rendered block 2',
            $renderedTemplate
        );
    }
}
