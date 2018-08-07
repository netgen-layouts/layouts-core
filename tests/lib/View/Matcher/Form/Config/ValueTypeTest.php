<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Form\Config;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Matcher\Stubs\Form;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Form\Config\ValueType;
use Netgen\BlockManager\View\View\FormView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Forms;

final class ValueTypeTest extends TestCase
{
    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp(): void
    {
        $this->formFactory = Forms::createFormFactoryBuilder()
            ->getFormFactory();

        $this->matcher = new ValueType();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Config\ValueType::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'configurable' => new Block(),
            ]
        );

        self::assertSame($expected, $this->matcher->match(new FormView($form), $config));
    }

    public function matchProvider(): array
    {
        return [
            [[], false],
            [[Query::class], false],
            [[Block::class], true],
            [[Query::class, Item::class], false],
            [[Block::class, Query::class], true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Config\ValueType::match
     */
    public function testMatchWithNoFormView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Config\ValueType::match
     */
    public function testMatchWithNoConfigurable(): void
    {
        $form = $this->formFactory->create(Form::class);

        self::assertFalse($this->matcher->match(new FormView($form), [Block::class]));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Config\ValueType::match
     */
    public function testMatchWithInvalidConfigurable(): void
    {
        $form = $this->formFactory->create(Form::class, null, ['configurable' => 'type']);

        self::assertFalse($this->matcher->match(new FormView($form), [Block::class]));
    }
}
