<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\View\Provider\RuleTargetViewProvider;
use Netgen\Layouts\View\View\RuleTargetViewInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(RuleTargetViewProvider::class)]
final class RuleTargetProviderTest extends TestCase
{
    private RuleTargetViewProvider $ruleTargetViewProvider;

    protected function setUp(): void
    {
        $this->ruleTargetViewProvider = new RuleTargetViewProvider();
    }

    public function testProvideView(): void
    {
        $target = Target::fromArray(['id' => Uuid::v4()]);

        $view = $this->ruleTargetViewProvider->provideView($target);

        self::assertInstanceOf(RuleTargetViewInterface::class, $view);

        self::assertSame($target, $view->target);
        self::assertNull($view->template);
        self::assertSame(
            [
                'target' => $target,
            ],
            $view->parameters,
        );
    }

    #[DataProvider('supportsDataProvider')]
    public function testSupports(mixed $value, bool $supports): void
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
