<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
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

        /** @var \Netgen\BlockManager\View\View\RuleConditionViewInterface $view */
        $view = $this->ruleConditionViewProvider->provideView($condition);

        $this->assertInstanceOf(RuleConditionViewInterface::class, $view);

        $this->assertSame($condition, $view->getCondition());
        $this->assertNull($view->getTemplate());
        $this->assertSame(
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
        $this->assertSame($supports, $this->ruleConditionViewProvider->supports($value));
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
