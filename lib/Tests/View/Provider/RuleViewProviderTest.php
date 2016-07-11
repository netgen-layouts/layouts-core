<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\Core\Values\LayoutResolver\RuleDraft;
use Netgen\BlockManager\Exception\NotFoundException;
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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutResolverServiceMock;

    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    protected $ruleViewProvider;

    public function setUp()
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $targetTypeRegistry = new TargetTypeRegistry();
        $targetTypeRegistry->addTargetType(new TargetType('target', 42));

        $conditionTypeRegistry = new ConditionTypeRegistry();
        $conditionTypeRegistry->addConditionType(new ConditionType('condition'));

        $this->ruleViewProvider = new RuleViewProvider(
            $this->layoutResolverServiceMock,
            $targetTypeRegistry,
            $conditionTypeRegistry
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\RuleViewProvider::provideView
     * @covers \Netgen\BlockManager\View\Provider\RuleViewProvider::loadPublishedRule
     */
    public function testProvideView()
    {
        $rule = new Rule(array('id' => 42));

        $this->layoutResolverServiceMock
            ->expects($this->never())
            ->method('loadRule');

        /** @var \Netgen\BlockManager\View\View\RuleViewInterface $view */
        $view = $this->ruleViewProvider->provideView($rule);

        self::assertInstanceOf(RuleViewInterface::class, $view);

        self::assertEquals($rule, $view->getRule());
        self::assertNull($view->getTemplate());
        self::assertEquals(
            array(
                'rule' => $rule,
                'published_rule' => $rule,
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
     * @covers \Netgen\BlockManager\View\Provider\RuleViewProvider::provideView
     * @covers \Netgen\BlockManager\View\Provider\RuleViewProvider::loadPublishedRule
     */
    public function testProvideViewWithRuleDraft()
    {
        $rule = new RuleDraft(array('id' => 42));
        $publishedRule = new Rule(array('id' => 42));

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadRule')
            ->with($this->equalTo(42))
            ->will($this->returnValue($publishedRule));

        /** @var \Netgen\BlockManager\View\View\RuleViewInterface $view */
        $view = $this->ruleViewProvider->provideView($rule);

        self::assertInstanceOf(RuleViewInterface::class, $view);

        self::assertEquals($rule, $view->getRule());
        self::assertNull($view->getTemplate());
        self::assertEquals(
            array(
                'rule' => $rule,
                'published_rule' => $publishedRule,
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
     * @covers \Netgen\BlockManager\View\Provider\RuleViewProvider::provideView
     * @covers \Netgen\BlockManager\View\Provider\RuleViewProvider::loadPublishedRule
     */
    public function testProvideViewWithRuleDraftWithoutPublishedStatus()
    {
        $rule = new RuleDraft(array('id' => 42));

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadRule')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException('rule', 42)));

        /** @var \Netgen\BlockManager\View\View\RuleViewInterface $view */
        $view = $this->ruleViewProvider->provideView($rule);

        self::assertInstanceOf(RuleViewInterface::class, $view);

        self::assertEquals($rule, $view->getRule());
        self::assertNull($view->getTemplate());
        self::assertEquals(
            array(
                'rule' => $rule,
                'published_rule' => null,
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
