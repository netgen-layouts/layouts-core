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

    public function setUp()
    {
        $this->formFactory = Forms::createFormFactoryBuilder()
            ->getFormFactory();

        $this->matcher = new Type();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Type::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
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

        $this->assertEquals($expected, $this->matcher->match(new FormView(['form_object' => $form]), $config));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Type::match
     */
    public function testMatchWithNullQueryType()
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

        $this->assertTrue($this->matcher->match(new FormView(['form_object' => $form]), ['null']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Type::match
     */
    public function testMatchWithNullQueryTypeReturnsFalse()
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

        $this->assertFalse($this->matcher->match(new FormView(['form_object' => $form]), ['test']));
    }

    /**
     * Provider for {@link self::testMatch}.
     *
     * @return array
     */
    public function matchProvider()
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
    public function testMatchWithNoFormView()
    {
        $this->assertFalse($this->matcher->match(new View(['value' => new Value()]), []));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Type::match
     */
    public function testMatchWithNoQuery()
    {
        $form = $this->formFactory->create(Form::class);

        $this->assertFalse($this->matcher->match(new FormView(['form_object' => $form]), ['type']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Type::match
     */
    public function testMatchWithInvalidQuery()
    {
        $form = $this->formFactory->create(Form::class, null, ['query' => 'type']);

        $this->assertFalse($this->matcher->match(new FormView(['form_object' => $form]), ['type']));
    }
}
