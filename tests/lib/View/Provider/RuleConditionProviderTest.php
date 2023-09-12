<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\View\Provider\RuleConditionViewProvider;
use Netgen\Layouts\View\View\RuleConditionViewInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class RuleConditionProviderTest extends TestCase
{
    private RuleConditionViewProvider $ruleConditionViewProvider;

    protected function setUp(): void
    {
        $this->ruleConditionViewProvider = new RuleConditionViewProvider();
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\RuleConditionViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $condition = RuleCondition::fromArray(['id' => Uuid::uuid4()]);

        $view = $this->ruleConditionViewProvider->provideView($condition);

        self::assertInstanceOf(RuleConditionViewInterface::class, $view);

        self::assertSame($condition, $view->getCondition());
        self::assertNull($view->getTemplate());
        self::assertSame(
            [
                'condition' => $condition,
            ],
            $view->getParameters(),
        );
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\View\Provider\RuleConditionViewProvider::supports
     *
     * @dataProvider supportsDataProvider
     */
    public function testSupports($value, bool $supports): void
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
