<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\View\Provider\RuleConditionViewProvider;
use Netgen\BlockManager\View\View\RuleConditionViewInterface;
use PHPUnit\Framework\TestCase;

class RuleConditionProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    protected $ruleConditionViewProvider;

    public function setUp()
    {
        $this->ruleConditionViewProvider = new RuleConditionViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\RuleConditionViewProvider::provideView
     */
    public function testProvideView()
    {
        $condition = new Condition(array('id' => 42));

        /** @var \Netgen\BlockManager\View\View\RuleConditionViewInterface $view */
        $view = $this->ruleConditionViewProvider->provideView($condition);

        self::assertInstanceOf(RuleConditionViewInterface::class, $view);

        self::assertEquals($condition, $view->getCondition());
        self::assertNull($view->getTemplate());
        self::assertEquals(
            array(
                'condition' => $condition,
            ),
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
        self::assertEquals($supports, $this->ruleConditionViewProvider->supports($value));
    }

    /**
     * Provider for {@link self::testSupports}.
     *
     * @return array
     */
    public function supportsProvider()
    {
        return array(
            array(new Condition(), true),
            array(new Rule(), false),
            array(new Target(), false),
        );
    }
}
