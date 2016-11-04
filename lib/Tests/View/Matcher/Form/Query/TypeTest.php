<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Form\Query;

use Netgen\BlockManager\Collection\QueryType;
use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandler;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Matcher\Stubs\Form;
use Netgen\BlockManager\View\View\FormView;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Form\Query\Type;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Forms;

class TypeTest extends TestCase
{
    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    protected $matcher;

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
            array(
                'query' => new Query(
                    array(
                        'queryType' => new QueryType(
                            'type',
                            new QueryTypeHandler(),
                            new Configuration('type', 'Type')
                        ),
                    )
                ),
            )
        );

        $this->assertEquals($expected, $this->matcher->match(new FormView($form), $config));
    }

    /**
     * Provider for {@link self::testMatch}.
     *
     * @return array
     */
    public function matchProvider()
    {
        return array(
            array(array(), false),
            array(array('other_type'), false),
            array(array('type'), true),
            array(array('other_type', 'second_type'), false),
            array(array('type', 'other_type'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Type::match
     */
    public function testMatchWithNoFormView()
    {
        $this->assertFalse($this->matcher->match(new View(new Value()), array()));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Type::match
     */
    public function testMatchWithNoQuery()
    {
        $form = $this->formFactory->create(Form::class);

        $this->assertFalse($this->matcher->match(new FormView($form), array('type')));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Type::match
     */
    public function testMatchWithInvalidQuery()
    {
        $form = $this->formFactory->create(Form::class, null, array('query' => 'type'));

        $this->assertFalse($this->matcher->match(new FormView($form), array('type')));
    }
}
