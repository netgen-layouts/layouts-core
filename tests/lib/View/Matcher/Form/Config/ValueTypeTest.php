<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Form\Config;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Matcher\Stubs\Form;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Form\Config\ValueType;
use Netgen\Layouts\View\View\FormView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

#[CoversClass(ValueType::class)]
final class ValueTypeTest extends TestCase
{
    private FormFactoryInterface $formFactory;

    private ValueType $matcher;

    protected function setUp(): void
    {
        $this->formFactory = Forms::createFormFactoryBuilder()
            ->getFormFactory();

        $this->matcher = new ValueType();
    }

    /**
     * @param mixed[] $config
     */
    #[DataProvider('matchDataProvider')]
    public function testMatch(array $config, bool $expected): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'configurable' => new Block(),
            ],
        );

        self::assertSame($expected, $this->matcher->match(new FormView($form), $config));
    }

    public static function matchDataProvider(): iterable
    {
        return [
            [[], false],
            [[Query::class], false],
            [[Block::class], true],
            [[Query::class, Item::class], false],
            [[Block::class, Query::class], true],
        ];
    }

    public function testMatchWithNoFormView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }

    public function testMatchWithNoConfigurable(): void
    {
        $form = $this->formFactory->create(Form::class);

        self::assertFalse($this->matcher->match(new FormView($form), [Block::class]));
    }

    public function testMatchWithInvalidConfigurable(): void
    {
        $form = $this->formFactory->create(Form::class, null, ['configurable' => 'type']);

        self::assertFalse($this->matcher->match(new FormView($form), [Block::class]));
    }
}
