<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\View\View\RuleConditionView;
use PHPUnit\Framework\TestCase;

final class RuleConditionViewTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\API\Values\LayoutResolver\Condition
     */
    private $condition;

    /**
     * @var \Netgen\Layouts\View\View\RuleConditionViewInterface
     */
    private $view;

    protected function setUp(): void
    {
        $this->condition = Condition::fromArray(['id' => 42]);

        $this->view = new RuleConditionView($this->condition);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('condition', 42);
    }

    /**
     * @covers \Netgen\Layouts\View\View\RuleConditionView::__construct
     * @covers \Netgen\Layouts\View\View\RuleConditionView::getCondition
     */
    public function testGetCondition(): void
    {
        self::assertSame($this->condition, $this->view->getCondition());
        self::assertSame(
            [
                'condition' => $this->condition,
                'param' => 'value',
            ],
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\Layouts\View\View\RuleConditionView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('rule_condition', $this->view::getIdentifier());
    }
}
