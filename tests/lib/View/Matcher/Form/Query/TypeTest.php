<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Form\Query;

use Netgen\BlockManager\Collection\QueryType\NullQueryType;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Matcher\Stubs\Form;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Form\Query\Type;
use Netgen\BlockManager\View\View\FormView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Forms;

final class TypeTest extends TestCase
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

        $this->matcher = new Type();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Type::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'query' => new Query(
                    [
                        'queryType' => new QueryType('type'),
                    ]
                ),
            ]
        );

        $this->assertSame($expected, $this->matcher->match(new FormView($form), $config));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Type::match
     */
    public function testMatchWithNullQueryType(): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'query' => new Query(
                    [
                        'queryType' => new NullQueryType('type'),
                    ]
                ),
            ]
        );

        $this->assertTrue($this->matcher->match(new FormView($form), ['null']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Type::match
     */
    public function testMatchWithNullQueryTypeReturnsFalse(): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'query' => new Query(
                    [
                        'queryType' => new NullQueryType('type'),
                    ]
                ),
            ]
        );

        $this->assertFalse($this->matcher->match(new FormView($form), ['test']));
    }

    public function matchProvider(): array
    {
        return [
            [[], false],
            [['other_type'], false],
            [['type'], true],
            [['other_type', 'second_type'], false],
            [['type', 'other_type'], true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Type::match
     */
    public function testMatchWithNoFormView(): void
    {
        $this->assertFalse($this->matcher->match(new View(new Value()), []));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Type::match
     */
    public function testMatchWithNoQuery(): void
    {
        $form = $this->formFactory->create(Form::class);

        $this->assertFalse($this->matcher->match(new FormView($form), ['type']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Type::match
     */
    public function testMatchWithInvalidQuery(): void
    {
        $form = $this->formFactory->create(Form::class, null, ['query' => 'type']);

        $this->assertFalse($this->matcher->match(new FormView($form), ['type']));
    }
}
