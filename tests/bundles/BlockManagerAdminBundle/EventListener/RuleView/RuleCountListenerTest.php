<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\EventListener\RuleView;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\View\RuleView;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\EventListener\RuleView\RuleCountListener;
use PHPUnit\Framework\TestCase;

class RuleCountListenerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutResolverServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\EventListener\RuleView\RuleCountListener
     */
    protected $listener;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->listener = new RuleCountListener($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\RuleView\RuleCountListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(BlockManagerEvents::BUILD_VIEW => 'onBuildView'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\RuleView\RuleCountListener::__construct
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\RuleView\RuleCountListener::onBuildView
     */
    public function testOnBuildView()
    {
        $view = new RuleView(array('rule' => new Rule(array('layout' => new Layout(array('published' => true))))));
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('getRuleCount')
            ->with($this->equalTo(new Layout(array('published' => true))))
            ->will($this->returnValue(3));

        $this->listener->onBuildView($event);

        $this->assertEquals(
            array(
                'rule_count' => 3,
            ),
            $event->getParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\RuleView\RuleCountListener::onBuildView
     */
    public function testOnBuildViewWithDraftLayout()
    {
        $view = new RuleView(array('rule' => new Rule(array('layout' => new Layout(array('published' => false))))));
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->layoutResolverServiceMock
            ->expects($this->never())
            ->method('getRuleCount');

        $this->listener->onBuildView($event);

        $this->assertEquals(
            array(
                'rule_count' => 0,
            ),
            $event->getParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\RuleView\RuleCountListener::onBuildView
     */
    public function testOnBuildViewWithNoRuleView()
    {
        $view = new View(array('value' => new Value()));
        $event = new CollectViewParametersEvent($view);
        $this->listener->onBuildView($event);

        $this->assertEquals(array(), $event->getParameters());
    }
}
