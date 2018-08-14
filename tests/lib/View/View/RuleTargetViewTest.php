<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\API\Values\LayoutResolver\Target;
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
        $this->target = Target::fromArray(['id' => 42]);

        $this->view = new RuleTargetView($this->target);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('target', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\RuleTargetView::__construct
     * @covers \Netgen\BlockManager\View\View\RuleTargetView::getTarget
     */
    public function testGetTarget(): void
    {
        self::assertSame($this->target, $this->view->getTarget());
        self::assertSame(
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
        self::assertSame('rule_target', $this->view::getIdentifier());
    }
}
