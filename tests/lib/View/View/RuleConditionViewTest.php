<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\View\View\RuleConditionView;
use PHPUnit\Framework\TestCase;

final class RuleConditionViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    private $condition;

    /**
     * @var \Netgen\BlockManager\View\View\RuleConditionViewInterface
     */
    private $view;

    public function setUp(): void
    {
        $this->condition = Condition::fromArray(['id' => 42]);

        $this->view = new RuleConditionView($this->condition);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('condition', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\RuleConditionView::__construct
     * @covers \Netgen\BlockManager\View\View\RuleConditionView::getCondition
     */
    public function testGetCondition(): void
    {
        $this->assertSame($this->condition, $this->view->getCondition());
        $this->assertSame(
            [
                'condition' => $this->condition,
                'param' => 'value',
            ],
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\RuleConditionView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('rule_condition', $this->view::getIdentifier());
    }
}
