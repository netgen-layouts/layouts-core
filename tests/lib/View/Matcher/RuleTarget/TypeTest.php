<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\RuleTarget;

use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\Layout\Resolver\TargetType\NullTargetType;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\TargetType1;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\RuleTarget\Type;
use Netgen\Layouts\View\View\RuleTargetView;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    private Type $matcher;

    protected function setUp(): void
    {
        $this->matcher = new Type();
    }

    /**
     * @param mixed[] $config
     *
     * @covers \Netgen\Layouts\View\Matcher\RuleTarget\Type::match
     *
     * @dataProvider matchDataProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $target = Target::fromArray(
            [
                'targetType' => new TargetType1(42),
            ],
        );

        $view = new RuleTargetView($target);

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\RuleTarget\Type::match
     */
    public function testMatchWithNullTargetType(): void
    {
        $target = Target::fromArray(
            [
                'targetType' => new NullTargetType(),
            ],
        );

        $view = new RuleTargetView($target);

        self::assertTrue($this->matcher->match($view, ['null']));
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\RuleTarget\Type::match
     */
    public function testMatchWithNullTargetTypeReturnsFalse(): void
    {
        $target = Target::fromArray(
            [
                'targetType' => new NullTargetType(),
            ],
        );

        $view = new RuleTargetView($target);

        self::assertFalse($this->matcher->match($view, ['test']));
    }

    public static function matchDataProvider(): iterable
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
     * @covers \Netgen\Layouts\View\Matcher\RuleTarget\Type::match
     */
    public function testMatchWithNoRuleTargetView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
