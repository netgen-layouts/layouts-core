<?php

namespace Netgen\BlockManager\Tests\View\Provider;

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
        $this->ruleViewProvider = new RuleViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\RuleViewProvider::provideView
     */
    public function testProvideView()
    {
        $rule = new Rule(array('id' => 42));

        /** @var \Netgen\BlockManager\View\View\RuleViewInterface $view */
        $view = $this->ruleViewProvider->provideView($rule);

        $this->assertInstanceOf(RuleViewInterface::class, $view);

        $this->assertEquals($rule, $view->getRule());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            array(
                'rule' => $rule,
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
        $this->assertEquals($supports, $this->ruleViewProvider->supports($value));
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
