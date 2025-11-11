<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\View\View\RuleView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(RuleView::class)]
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

    public function testGetRule(): void
    {
        self::assertSame($this->rule, $this->view->getRule());
        self::assertSame(
            [
                'param' => 'value',
                'rule' => $this->rule,
            ],
            $this->view->getParameters(),
        );
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('rule', $this->view::getIdentifier());
    }
}
