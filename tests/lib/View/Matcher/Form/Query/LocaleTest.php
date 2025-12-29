<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Form\Query;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Matcher\Stubs\Form;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Form\Query\Locale;
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
                'query' => Query::fromArray(
                    [
                        'locale' => 'en',
                    ],
                ),
            ],
        );

        self::assertSame($expected, $this->matcher->match(new FormView($form), $config));
    }

    /**
     * @return iterable<mixed>
     */
    public static function matchDataProvider(): iterable
    {
        return [
            [[], false],
            [['fr'], false],
            [['en'], true],
            [['fr', 'de'], false],
            [['en', 'fr'], true],
        ];
    }

    public function testMatchWithNoFormView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }

    public function testMatchWithNoQuery(): void
    {
        $form = $this->formFactory->create(Form::class);

        self::assertFalse($this->matcher->match(new FormView($form), ['Locale']));
    }

    public function testMatchWithInvalidQuery(): void
    {
        $form = $this->formFactory->create(Form::class, null, ['query' => 'Locale']);

        self::assertFalse($this->matcher->match(new FormView($form), ['Locale']));
    }
}
