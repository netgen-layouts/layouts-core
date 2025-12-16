<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\View\Provider\RuleConditionViewProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(RuleConditionViewProvider::class)]
final class RuleConditionProviderTest extends TestCase
{
    private RuleConditionViewProvider $ruleConditionViewProvider;

    protected function setUp(): void
    {
        $this->ruleConditionViewProvider = new RuleConditionViewProvider();
    }

    public function testProvideView(): void
    {
        $condition = RuleCondition::fromArray(['id' => Uuid::v4()]);

        $view = $this->ruleConditionViewProvider->provideView($condition);

        self::assertSame($condition, $view->condition);
        self::assertNull($view->template);
        self::assertSame(
            [
                'condition' => $condition,
            ],
            $view->parameters,
        );
    }

    #[DataProvider('supportsDataProvider')]
    public function testSupports(object $value, bool $supports): void
    {
        self::assertSame($supports, $this->ruleConditionViewProvider->supports($value));
    }

    public static function supportsDataProvider(): iterable
    {
        return [
            [new RuleCondition(), true],
            [new Rule(), false],
            [new Target(), false],
        ];
    }
}
