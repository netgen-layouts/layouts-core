<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\View\View\RuleConditionView;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class RuleConditionViewTest extends TestCase
{
    private RuleCondition $condition;

    private RuleConditionView $view;

    protected function setUp(): void
    {
        $this->condition = RuleCondition::fromArray(['id' => Uuid::uuid4()]);

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
            $this->view->getParameters(),
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
