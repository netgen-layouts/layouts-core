<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\View\Provider\RuleTargetViewProvider;
use Netgen\BlockManager\View\View\RuleTargetViewInterface;
use PHPUnit\Framework\TestCase;

final class RuleTargetProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    private $ruleTargetViewProvider;

    public function setUp(): void
    {
        $this->ruleTargetViewProvider = new RuleTargetViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\RuleTargetViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $target = new Target(['id' => 42]);

        /** @var \Netgen\BlockManager\View\View\RuleTargetViewInterface $view */
        $view = $this->ruleTargetViewProvider->provideView($target);

        $this->assertInstanceOf(RuleTargetViewInterface::class, $view);

        $this->assertSame($target, $view->getTarget());
        $this->assertNull($view->getTemplate());
        $this->assertSame(
            [
                'target' => $target,
            ],
            $view->getParameters()
        );
    }

    /**
     * @param mixed $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\RuleTargetViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, bool $supports): void
    {
        $this->assertSame($supports, $this->ruleTargetViewProvider->supports($value));
    }

    public function supportsProvider(): array
    {
        return [
            [new Target(), true],
            [new Rule(), false],
            [new Condition(), false],
        ];
    }
}
