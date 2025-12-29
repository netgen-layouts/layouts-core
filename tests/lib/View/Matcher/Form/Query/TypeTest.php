<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Form\Query;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\QueryType\NullQueryType;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use Netgen\Layouts\Tests\View\Matcher\Stubs\Form;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Form\Query\Type;
use Netgen\Layouts\View\View\FormView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

#[CoversClass(Type::class)]
final class TypeTest extends TestCase
{
    private FormFactoryInterface $formFactory;

    private Type $matcher;

    protected function setUp(): void
    {
        $this->formFactory = Forms::createFormFactoryBuilder()
            ->getFormFactory();

        $this->matcher = new Type();
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
                        'queryType' => new QueryType('type'),
                    ],
                ),
            ],
        );

        self::assertSame($expected, $this->matcher->match(new FormView($form), $config));
    }

    public function testMatchWithNullQueryType(): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'query' => Query::fromArray(
                    [
                        'queryType' => new NullQueryType('type'),
                    ],
                ),
            ],
        );

        self::assertTrue($this->matcher->match(new FormView($form), ['null']));
    }

    public function testMatchWithNullQueryTypeReturnsFalse(): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'query' => Query::fromArray(
                    [
                        'queryType' => new NullQueryType('type'),
                    ],
                ),
            ],
        );

        self::assertFalse($this->matcher->match(new FormView($form), ['test']));
    }

    /**
     * @return iterable<mixed>
     */
    public static function matchDataProvider(): iterable
    {
        return [
            [[], false],
            [['other_type'], false],
            [['type'], true],
            [['other_type', 'second_type'], false],
            [['type', 'other_type'], true],
        ];
    }

    public function testMatchWithNoFormView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }

    public function testMatchWithNoQuery(): void
    {
        $form = $this->formFactory->create(Form::class);

        self::assertFalse($this->matcher->match(new FormView($form), ['type']));
    }

    public function testMatchWithInvalidQuery(): void
    {
        $form = $this->formFactory->create(Form::class, null, ['query' => 'type']);

        self::assertFalse($this->matcher->match(new FormView($form), ['type']));
    }
}
