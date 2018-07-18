<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Parameter;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\TextType;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Parameter\Type;
use Netgen\BlockManager\View\View\ParameterView;
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
     * @covers \Netgen\BlockManager\View\Matcher\Parameter\Type::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $parameter = Parameter::fromArray(
            [
                'parameterDefinition' => ParameterDefinition::fromArray(
                    [
                        'type' => new TextType(),
                    ]
                ),
            ]
        );

        $view = new ParameterView($parameter);

        $this->assertSame($expected, $this->matcher->match($view, $config));
    }

    public function matchProvider(): array
    {
        return [
            [[], false],
            [['boolean'], false],
            [['text'], true],
            [['boolean', 'integer'], false],
            [['boolean', 'text'], true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Parameter\Type::match
     */
    public function testMatchWithNoParameterView(): void
    {
        $this->assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
