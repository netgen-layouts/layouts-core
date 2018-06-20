<?php

declare(strict_types=1);

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

    public function setUp(): void
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
    public function testGetTarget(): void
    {
        $this->assertSame($this->target, $this->view->getTarget());
        $this->assertSame(
            [
                'target' => $this->target,
                'param' => 'value',
            ],
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\RuleTargetView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('rule_target', $this->view->getIdentifier());
    }
}
