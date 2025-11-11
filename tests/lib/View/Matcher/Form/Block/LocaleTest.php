<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Form\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Matcher\Stubs\Form;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Form\Block\Locale;
use Netgen\Layouts\View\View\FormView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

#[CoversClass(Locale::class)]
final class LocaleTest extends TestCase
{
    private FormFactoryInterface $formFactory;

    private Locale $matcher;

    protected function setUp(): void
    {
        $this->formFactory = Forms::createFormFactoryBuilder()
            ->getFormFactory();

        $this->matcher = new Locale();
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
                        'locale' => 'en',
                    ],
                ),
            ],
        );

        self::assertSame($expected, $this->matcher->match(new FormView($form), $config));
    }

    public static function matchDataProvider(): iterable
    {
        return [
            [[], false],
            [['de'], false],
            [['en'], true],
            [['de', 'fr'], false],
            [['en', 'de'], true],
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
