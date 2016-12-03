<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\EventListener\RuleView;

use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use Netgen\Bundle\BlockManagerAdminBundle\EventListener\RuleView\RuleViewListener;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\View\RuleView;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\View\ViewInterface;
use PHPUnit\Framework\TestCase;

class RuleViewListenerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    protected $targetTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    protected $conditionTypeRegistry;

    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\EventListener\RuleView\RuleViewListener
     */
    protected $listener;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->targetTypeRegistry = new TargetTypeRegistry();
        $this->conditionTypeRegistry = new ConditionTypeRegistry();

        $this->targetTypeRegistry->addTargetType(new TargetType('type'));
        $this->conditionTypeRegistry->addConditionType(new ConditionType('type'));

        $this->listener = new RuleViewListener(
            $this->targetTypeRegistry,
            $this->conditionTypeRegistry
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\RuleView\RuleViewListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(BlockManagerEvents::BUILD_VIEW => 'onBuildView'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\RuleView\RuleViewListener::__construct
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\RuleView\RuleViewListener::onBuildView
     */
    public function testOnBuildView()
    {
        $view = new RuleView(array('valueObject' => new Rule()));
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        $this->assertEquals(
            array(
                'target_types' => $this->targetTypeRegistry->getTargetTypes(),
                'condition_types' => $this->conditionTypeRegistry->getConditionTypes(),
            ),
            $event->getParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\RuleView\RuleViewListener::onBuildView
     */
    public function testOnBuildViewWithNoRuleView()
    {
        $view = new View(array('valueObject' => new Value()));
        $event = new CollectViewParametersEvent($view);
        $this->listener->onBuildView($event);

        $this->assertEquals(array(), $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\RuleView\RuleViewListener::onBuildView
     */
    public function testOnBuildViewWithWrongContext()
    {
        $view = new RuleView(array('valueObject' => new Rule()));
        $view->setContext(ViewInterface::CONTEXT_API);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        $this->assertEquals(array(), $event->getParameters());
    }
}
