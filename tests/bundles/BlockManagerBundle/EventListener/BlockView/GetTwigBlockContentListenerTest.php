<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\BlockView;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\TwigBlockDefinition;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener;
use PHPUnit\Framework\TestCase;
use stdClass;
use Twig\Template;

class GetTwigBlockContentListenerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener
     */
    private $listener;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->listener = new GetTwigBlockContentListener();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(BlockManagerEvents::RENDER_VIEW => 'onRenderView'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::onRenderView
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::getTwigBlockContent
     */
    public function testOnRenderView()
    {
        $block = new Block(
            array(
                'id' => 42,
                'definition' => new TwigBlockDefinition('block_definition'),
            )
        );

        $blockView = new BlockView(array('block' => $block));

        $twigTemplateMock = $this->createMock(Template::class);

        $twigTemplateMock
            ->expects($this->once())
            ->method('hasBlock')
            ->will($this->returnValue(true));

        $twigTemplateMock
            ->expects($this->once())
            ->method('displayBlock')
            ->will($this->returnCallback(function () { echo 'rendered twig block'; }));

        $blockView->addParameter('twig_template', new ContextualizedTwigTemplate($twigTemplateMock));

        $event = new CollectViewParametersEvent($blockView);
        $this->listener->onRenderView($event);

        $this->assertArrayHasKey('twig_content', $event->getParameters());
        $this->assertEquals('rendered twig block', $event->getParameters()['twig_content']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::onRenderView
     */
    public function testOnRenderViewWithNoTwigBlock()
    {
        $block = new Block(
            array(
                'id' => 42,
                'definition' => new BlockDefinition('block_definition'),
            )
        );

        $blockView = new BlockView(array('block' => $block));
        $event = new CollectViewParametersEvent($blockView);
        $this->listener->onRenderView($event);

        $this->assertEquals(array(), $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::onRenderView
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::getTwigBlockContent
     */
    public function testOnRenderViewInvalidTwigTemplate()
    {
        $block = new Block(
            array(
                'id' => 42,
                'definition' => new TwigBlockDefinition('block_definition'),
            )
        );

        $blockView = new BlockView(array('block' => $block));
        $blockView->addParameter('twig_template', new stdClass());

        $event = new CollectViewParametersEvent($blockView);
        $this->listener->onRenderView($event);

        $this->assertArrayHasKey('twig_content', $event->getParameters());
        $this->assertEquals('', $event->getParameters()['twig_content']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::onRenderView
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::getTwigBlockContent
     */
    public function testOnRenderViewWithNoTwigTemplate()
    {
        $block = new Block(
            array(
                'id' => 42,
                'definition' => new TwigBlockDefinition('block_definition'),
            )
        );

        $blockView = new BlockView(array('block' => $block));

        $event = new CollectViewParametersEvent($blockView);
        $this->listener->onRenderView($event);

        $this->assertArrayHasKey('twig_content', $event->getParameters());
        $this->assertEquals('', $event->getParameters()['twig_content']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::onRenderView
     */
    public function testOnRenderViewWithNoBlockView()
    {
        $view = new View(array('value' => new Value()));
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        $this->assertEquals(array(), $event->getParameters());
    }
}
