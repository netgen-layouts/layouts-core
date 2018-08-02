<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\RuleTarget;

use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Layout\Resolver\TargetType\NullTargetType;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType1;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\RuleTarget\Type;
use Netgen\BlockManager\View\View\RuleTargetView;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp(): void
    {
        $this->matcher = new Type();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\RuleTarget\Type::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $target = Target::fromArray(
            [
                'targetType' => new TargetType1('target1'),
            ]
        );

        $view = new RuleTargetView($target);

        $this->assertSame($expected, $this->matcher->match($view, $config));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\RuleTarget\Type::match
     */
    public function testMatchWithNullTargetType(): void
    {
        $target = Target::fromArray(
            [
                'targetType' => new NullTargetType(),
            ]
        );

        $view = new RuleTargetView($target);

        $this->assertTrue($this->matcher->match($view, ['null']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\RuleTarget\Type::match
     */
    public function testMatchWithNullTargetTypeReturnsFalse(): void
    {
        $target = Target::fromArray(
            [
                'targetType' => new NullTargetType(),
            ]
        );

        $view = new RuleTargetView($target);

        $this->assertFalse($this->matcher->match($view, ['test']));
    }

    public function matchProvider(): array
    {
        return [
            [[], false],
            [['some_type'], false],
            [['target1'], true],
            [['some_type', 'some_type_2'], false],
            [['some_type', 'target1'], true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\RuleTarget\Type::match
     */
    public function testMatchWithNoRuleTargetView(): void
    {
        $this->assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
