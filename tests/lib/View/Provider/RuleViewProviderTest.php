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

    public function setUp(): void
    {
        $this->ruleViewProvider = new RuleViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\RuleViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $rule = Rule::fromArray(['id' => 42]);

        $view = $this->ruleViewProvider->provideView($rule);

        self::assertInstanceOf(RuleViewInterface::class, $view);

        self::assertSame($rule, $view->getRule());
        self::assertNull($view->getTemplate());
        self::assertSame(
            [
                'rule' => $rule,
            ],
            $view->getParameters()
        );
    }

    /**
     * @param mixed $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\RuleViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, bool $supports): void
    {
        self::assertSame($supports, $this->ruleViewProvider->supports($value));
    }

    public function supportsProvider(): array
    {
        return [
            [new Value(), false],
            [new Block(), false],
            [new Rule(), true],
        ];
    }
}
