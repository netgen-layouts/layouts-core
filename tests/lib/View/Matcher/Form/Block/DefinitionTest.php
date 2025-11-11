<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Form\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\NullBlockDefinition;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Matcher\Stubs\Form;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Block\DefinitionTrait;
use Netgen\Layouts\View\Matcher\Form\Block\Definition;
use Netgen\Layouts\View\View\FormView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

#[CoversClass(Definition::class)]
#[CoversClass(DefinitionTrait::class)]
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
     */
    #[DataProvider('matchDataProvider')]
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

    public function testMatchWithNoFormView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }

    public function testMatchWithNoBlock(): void
    {
        $form = $this->formFactory->create(Form::class);

        self::assertFalse($this->matcher->match(new FormView($form), ['block']));
    }

    public function testMatchWithInvalidBlock(): void
    {
        $form = $this->formFactory->create(Form::class, null, ['block' => 'block']);

        self::assertFalse($this->matcher->match(new FormView($form), ['block']));
    }
}
