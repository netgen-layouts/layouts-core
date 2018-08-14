<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\View\Provider\RuleConditionViewProvider;
use Netgen\BlockManager\View\View\RuleConditionViewInterface;
use PHPUnit\Framework\TestCase;

final class RuleConditionProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    private $ruleConditionViewProvider;

    public function setUp(): void
    {
        $this->ruleConditionViewProvider = new RuleConditionViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\RuleConditionViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $condition = Condition::fromArray(['id' => 42]);

        $view = $this->ruleConditionViewProvider->provideView($condition);

        self::assertInstanceOf(RuleConditionViewInterface::class, $view);

        self::assertSame($condition, $view->getCondition());
        self::assertNull($view->getTemplate());
        self::assertSame(
            [
                'condition' => $condition,
            ],
            $view->getParameters()
        );
    }

    /**
     * @param mixed $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\RuleConditionViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, bool $supports): void
    {
        self::assertSame($supports, $this->ruleConditionViewProvider->supports($value));
    }

    public function supportsProvider(): array
    {
        return [
            [new Condition(), true],
            [new Rule(), false],
            [new Target(), false],
        ];
    }
}
