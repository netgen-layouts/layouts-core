<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\View\View\RuleView;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class RuleViewTest extends TestCase
{
    private Rule $rule;

    private RuleView $view;

    protected function setUp(): void
    {
        $this->rule = Rule::fromArray(['id' => Uuid::uuid4()]);

        $this->view = new RuleView($this->rule);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('rule', 42);
    }

    /**
     * @covers \Netgen\Layouts\View\View\RuleView::__construct
     * @covers \Netgen\Layouts\View\View\RuleView::getRule
     */
    public function testGetRule(): void
    {
        self::assertSame($this->rule, $this->view->getRule());
        self::assertSame(
            [
                'rule' => $this->rule,
                'param' => 'value',
            ],
            $this->view->getParameters(),
        );
    }

    /**
     * @covers \Netgen\Layouts\View\View\RuleView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('rule', $this->view::getIdentifier());
    }
}
