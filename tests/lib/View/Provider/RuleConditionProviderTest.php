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

    public function setUp()
    {
        $this->ruleConditionViewProvider = new RuleConditionViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\RuleConditionViewProvider::provideView
     */
    public function testProvideView()
    {
        $condition = new Condition(['id' => 42]);

        /** @var \Netgen\BlockManager\View\View\RuleConditionViewInterface $view */
        $view = $this->ruleConditionViewProvider->provideView($condition);

        $this->assertInstanceOf(RuleConditionViewInterface::class, $view);

        $this->assertEquals($condition, $view->getCondition());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            [
                'condition' => $condition,
            ],
            $view->getParameters()
        );
    }

    /**
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\RuleConditionViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, $supports)
    {
        $this->assertEquals($supports, $this->ruleConditionViewProvider->supports($value));
    }

    /**
     * Provider for {@link self::testSupports}.
     *
     * @return array
     */
    public function supportsProvider()
    {
        return [
            [new Condition(), true],
            [new Rule(), false],
            [new Target(), false],
        ];
    }
}
