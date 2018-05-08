<?php

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\View\View\RuleTargetView;
use PHPUnit\Framework\TestCase;

final class RuleTargetViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    private $target;

    /**
     * @var \Netgen\BlockManager\View\View\RuleTargetViewInterface
     */
    private $view;

    public function setUp()
    {
        $this->target = new Target(['id' => 42]);

        $this->view = new RuleTargetView(
            [
                'target' => $this->target,
            ]
        );

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('target', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\RuleTargetView::__construct
     * @covers \Netgen\BlockManager\View\View\RuleTargetView::getTarget
     */
    public function testGetTarget()
    {
        $this->assertEquals($this->target, $this->view->getTarget());
        $this->assertEquals(
            [
                'param' => 'value',
                'target' => $this->target,
            ],
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\RuleTargetView::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('rule_target_view', $this->view->getIdentifier());
    }
}
