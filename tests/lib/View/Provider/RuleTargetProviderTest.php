<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\View\Provider\RuleTargetViewProvider;
use Netgen\Layouts\View\View\RuleTargetViewInterface;
use PHPUnit\Framework\TestCase;

final class RuleTargetProviderTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\View\Provider\ViewProviderInterface
     */
    private $ruleTargetViewProvider;

    public function setUp(): void
    {
        $this->ruleTargetViewProvider = new RuleTargetViewProvider();
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\RuleTargetViewProvider::provideView
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
     * @covers \Netgen\Layouts\View\Provider\RuleTargetViewProvider::supports
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
