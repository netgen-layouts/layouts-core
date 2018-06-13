<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\Provider\RuleViewProvider;
use Netgen\BlockManager\View\View\RuleViewInterface;
use PHPUnit\Framework\TestCase;

final class RuleViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    private $ruleViewProvider;

    public function setUp()
    {
        $this->ruleViewProvider = new RuleViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\RuleViewProvider::provideView
     */
    public function testProvideView()
    {
        $rule = new Rule(['id' => 42]);

        /** @var \Netgen\BlockManager\View\View\RuleViewInterface $view */
        $view = $this->ruleViewProvider->provideView($rule);

        $this->assertInstanceOf(RuleViewInterface::class, $view);

        $this->assertEquals($rule, $view->getRule());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            [
                'rule' => $rule,
            ],
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
        return [
            [new Value(), false],
            [new Block(), false],
            [new Rule(), true],
        ];
    }
}
