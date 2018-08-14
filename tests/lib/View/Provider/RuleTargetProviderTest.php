<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
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
        $target = Target::fromArray(['id' => 42]);

        $view = $this->ruleTargetViewProvider->provideView($target);

        self::assertInstanceOf(RuleTargetViewInterface::class, $view);

        self::assertSame($target, $view->getTarget());
        self::assertNull($view->getTemplate());
        self::assertSame(
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
        self::assertSame($supports, $this->ruleTargetViewProvider->supports($value));
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
