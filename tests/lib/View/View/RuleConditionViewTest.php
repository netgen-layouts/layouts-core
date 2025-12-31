<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\View\View\RuleConditionView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(RuleConditionView::class)]
final class RuleConditionViewTest extends TestCase
{
    private RuleCondition $condition;

    private RuleConditionView $view;

    protected function setUp(): void
    {
        $this->condition = RuleCondition::fromArray(['id' => Uuid::v7()]);

        $this->view = new RuleConditionView($this->condition);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('condition', 42);
    }

    public function testGetCondition(): void
    {
        self::assertSame($this->condition, $this->view->condition);
        self::assertSame(
            [
                'param' => 'value',
                'condition' => $this->condition,
            ],
            $this->view->parameters,
        );
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('rule_condition', $this->view->identifier);
    }
}
