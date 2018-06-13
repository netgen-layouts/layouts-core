<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Form\Query;

use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Matcher\Stubs\Form;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Form\Query\Locale;
use Netgen\BlockManager\View\View\FormView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Forms;

final class LocaleTest extends TestCase
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
            [
                'query' => new Query(
                    [
                        'locale' => 'en',
                    ]
                ),
            ]
        );

        $this->assertEquals($expected, $this->matcher->match(new FormView(['form_object' => $form]), $config));
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
            [['fr'], false],
            [['en'], true],
            [['fr', 'de'], false],
            [['en', 'fr'], true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Locale::match
     */
    public function testMatchWithNoFormView()
    {
        $this->assertFalse($this->matcher->match(new View(['value' => new Value()]), []));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Locale::match
     */
    public function testMatchWithNoQuery()
    {
        $form = $this->formFactory->create(Form::class);

        $this->assertFalse($this->matcher->match(new FormView(['form_object' => $form]), ['Locale']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Query\Locale::match
     */
    public function testMatchWithInvalidQuery()
    {
        $form = $this->formFactory->create(Form::class, null, ['query' => 'Locale']);

        $this->assertFalse($this->matcher->match(new FormView(['form_object' => $form]), ['Locale']));
    }
}
