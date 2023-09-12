<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\View\Provider\RuleTargetViewProvider;
use Netgen\Layouts\View\View\RuleTargetViewInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class RuleTargetProviderTest extends TestCase
{
    private RuleTargetViewProvider $ruleTargetViewProvider;

    protected function setUp(): void
    {
        $this->ruleTargetViewProvider = new RuleTargetViewProvider();
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\RuleTargetViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $target = Target::fromArray(['id' => Uuid::uuid4()]);

        $view = $this->ruleTargetViewProvider->provideView($target);

        self::assertInstanceOf(RuleTargetViewInterface::class, $view);

        self::assertSame($target, $view->getTarget());
        self::assertNull($view->getTemplate());
        self::assertSame(
            [
                'target' => $target,
            ],
            $view->getParameters(),
        );
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\View\Provider\RuleTargetViewProvider::supports
     *
     * @dataProvider supportsDataProvider
     */
    public function testSupports($value, bool $supports): void
    {
        self::assertSame($supports, $this->ruleTargetViewProvider->supports($value));
    }

    public static function supportsDataProvider(): iterable
    {
        return [
            [new Target(), true],
            [new Rule(), false],
            [new RuleCondition(), false],
        ];
    }
}
