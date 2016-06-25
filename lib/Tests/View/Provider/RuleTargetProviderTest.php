<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\View\Provider\RuleTargetViewProvider;
use Netgen\BlockManager\View\RuleTargetViewInterface;
use PHPUnit\Framework\TestCase;

class RuleTargetProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    protected $ruleTargetViewProvider;

    public function setUp()
    {
        $this->ruleTargetViewProvider = new RuleTargetViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\RuleTargetViewProvider::provideView
     */
    public function testProvideView()
    {
        $target = new Target(array('id' => 42));

        /** @var \Netgen\BlockManager\View\RuleTargetViewInterface $view */
        $view = $this->ruleTargetViewProvider->provideView($target);

        self::assertInstanceOf(RuleTargetViewInterface::class, $view);

        self::assertEquals($target, $view->getTarget());
        self::assertNull($view->getTemplate());
        self::assertEquals(
            array(
                'target' => $target,
            ),
            $view->getParameters()
        );
    }

    /**
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\RuleTargetViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, $supports)
    {
        self::assertEquals($supports, $this->ruleTargetViewProvider->supports($value));
    }

    /**
     * Provider for {@link self::testSupports}.
     *
     * @return array
     */
    public function supportsProvider()
    {
        return array(
            array(new Target(), true),
            array(new Rule(), false),
            array(new Condition(), false),
        );
    }
}
