<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Form;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Matcher\Stubs\FormView;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Form\Type;
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
     * @covers \Netgen\BlockManager\View\Matcher\Form\Type::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $view = new FormView();

        $this->assertEquals($expected, $this->matcher->match($view, $config));
    }

    public function matchProvider(): array
    {
        return [
            [[], false],
            [['other_form_type'], false],
            [['form_type'], true],
            [['other_form_type', 'second_form_type'], false],
            [['form_type', 'other_form_type'], true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Type::match
     */
    public function testMatchWithNoFormView(): void
    {
        $this->assertFalse($this->matcher->match(new View(['value' => new Value()]), []));
    }
}
