<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\View\View\RuleTargetView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(RuleTargetView::class)]
final class RuleTargetViewTest extends TestCase
{
    private Target $target;

    private RuleTargetView $view;

    protected function setUp(): void
    {
        $this->target = Target::fromArray(['id' => Uuid::uuid4()]);

        $this->view = new RuleTargetView($this->target);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('target', 42);
    }

    public function testGetTarget(): void
    {
        self::assertSame($this->target, $this->view->getTarget());
        self::assertSame(
            [
                'param' => 'value',
                'target' => $this->target,
            ],
            $this->view->getParameters(),
        );
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('rule_target', $this->view::getIdentifier());
    }
}
