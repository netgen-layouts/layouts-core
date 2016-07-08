<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use Netgen\BlockManager\View\Provider\RuleViewProvider;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\View\RuleViewInterface;
use PHPUnit\Framework\TestCase;

class RuleViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    protected $ruleViewProvider;

    public function setUp()
    {
        $targetTypeRegistry = new TargetTypeRegistry();
        $targetTypeRegistry->addTargetType(new TargetType('target', 42));

        $conditionTypeRegistry = new ConditionTypeRegistry();
        $conditionTypeRegistry->addConditionType(new ConditionType('condition'));

        $this->ruleViewProvider = new RuleViewProvider(
            $targetTypeRegistry,
            $conditionTypeRegistry
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\RuleViewProvider::provideView
     */
    public function testProvideView()
    {
        $rule = new Rule(array('id' => 42));

        /** @var \Netgen\BlockManager\View\View\RuleViewInterface $view */
        $view = $this->ruleViewProvider->provideView($rule);

        self::assertInstanceOf(RuleViewInterface::class, $view);

        self::assertEquals($rule, $view->getRule());
        self::assertNull($view->getTemplate());
        self::assertEquals(
            array(
                'rule' => $rule,
                'target_types' => array(
                    'target' => new TargetType('target', 42),
                ),
                'condition_types' => array(
                    'condition' => new ConditionType('condition'),
                ),
            ),
            $view->getParameters()
        );
    }

    /**
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\RuleViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, $supports)
    {
        self::assertEquals($supports, $this->ruleViewProvider->supports($value));
    }

    /**
     * Provider for {@link self::testSupports}.
     *
     * @return array
     */
    public function supportsProvider()
    {
        return array(
            array(new Value(), false),
            array(new Block(), false),
            array(new Rule(), true),
        );
    }
}
