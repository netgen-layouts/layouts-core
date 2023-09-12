<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Form;

use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Matcher\Stubs\FormView;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Form\Type;
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
     * @covers \Netgen\Layouts\View\Matcher\Form\Type::match
     *
     * @dataProvider matchDataProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $view = new FormView();

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    public static function matchDataProvider(): iterable
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
     * @covers \Netgen\Layouts\View\Matcher\Form\Type::match
     */
    public function testMatchWithNoFormView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
