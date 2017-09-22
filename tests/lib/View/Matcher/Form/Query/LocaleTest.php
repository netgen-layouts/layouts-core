<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Form\Query;

use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Matcher\Stubs\Form;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Form\Query\Locale;
use Netgen\BlockManager\View\View\FormView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Forms;

class LocaleTest extends TestCase
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

        $this->matcher = new Locale();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Locale::match
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
                        'locale' => 'en',
                    )
                ),
            )
        );

        $this->assertEquals($expected, $this->matcher->match(new FormView(array('form_object' => $form)), $config));
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
            array(array('fr'), false),
            array(array('en'), true),
            array(array('fr', 'de'), false),
            array(array('en', 'fr'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Locale::match
     */
    public function testMatchWithNoFormView()
    {
        $this->assertFalse($this->matcher->match(new View(array('value' => new Value())), array()));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Locale::match
     */
    public function testMatchWithNoQuery()
    {
        $form = $this->formFactory->create(Form::class);

        $this->assertFalse($this->matcher->match(new FormView(array('form_object' => $form)), array('Locale')));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Locale::match
     */
    public function testMatchWithInvalidQuery()
    {
        $form = $this->formFactory->create(Form::class, null, array('query' => 'Locale'));

        $this->assertFalse($this->matcher->match(new FormView(array('form_object' => $form)), array('Locale')));
    }
}
