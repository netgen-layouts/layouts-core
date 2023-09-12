<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Form\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\NullBlockDefinition;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Matcher\Stubs\Form;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Form\Block\Definition;
use Netgen\Layouts\View\View\FormView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

final class DefinitionTest extends TestCase
{
    private FormFactoryInterface $formFactory;

    private Definition $matcher;

    protected function setUp(): void
    {
        $this->formFactory = Forms::createFormFactoryBuilder()
            ->getFormFactory();

        $this->matcher = new Definition();
    }

    /**
     * @param mixed[] $config
     *
     * @covers \Netgen\Layouts\View\Matcher\Form\Block\Definition::match
     *
     * @dataProvider matchDataProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'block' => Block::fromArray(
                    [
                        'definition' => BlockDefinition::fromArray(['identifier' => 'block']),
                    ],
                ),
            ],
        );

        self::assertSame($expected, $this->matcher->match(new FormView($form), $config));
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Block\DefinitionTrait::doMatch
     * @covers \Netgen\Layouts\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithNullBlockDefinition(): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'block' => Block::fromArray(
                    [
                        'definition' => new NullBlockDefinition('definition'),
                    ],
                ),
            ],
        );

        self::assertTrue($this->matcher->match(new FormView($form), ['null']));
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Block\DefinitionTrait::doMatch
     * @covers \Netgen\Layouts\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithNullBlockDefinitionReturnsFalse(): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'block' => Block::fromArray(
                    [
                        'definition' => new NullBlockDefinition('definition'),
                    ],
                ),
            ],
        );

        self::assertFalse($this->matcher->match(new FormView($form), ['test']));
    }

    public static function matchDataProvider(): iterable
    {
        return [
            [[], false],
            [['other_block'], false],
            [['block'], true],
            [['other_block', 'second_block'], false],
            [['block', 'other_block'], true],
        ];
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithNoFormView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithNoBlock(): void
    {
        $form = $this->formFactory->create(Form::class);

        self::assertFalse($this->matcher->match(new FormView($form), ['block']));
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithInvalidBlock(): void
    {
        $form = $this->formFactory->create(Form::class, null, ['block' => 'block']);

        self::assertFalse($this->matcher->match(new FormView($form), ['block']));
    }
}
