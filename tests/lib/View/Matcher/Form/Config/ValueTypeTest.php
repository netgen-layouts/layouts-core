<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Form\Config;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Matcher\Stubs\Form;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Form\Config\ValueType;
use Netgen\BlockManager\View\View\FormView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Forms;

class ValueTypeTest extends TestCase
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

        $this->matcher = new ValueType();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Form\Config\ValueType::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            array(
                'configurable' => new Block(),
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
            array(array(Query::class), false),
            array(array(Block::class), true),
            array(array(Query::class, Item::class), false),
            array(array(Block::class, Query::class), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Config\ValueType::match
     */
    public function testMatchWithNoFormView()
    {
        $this->assertFalse($this->matcher->match(new View(array('value' => new Value())), array()));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Config\ValueType::match
     */
    public function testMatchWithNoConfigurable()
    {
        $form = $this->formFactory->create(Form::class);

        $this->assertFalse($this->matcher->match(new FormView(array('form_object' => $form)), array(Block::class)));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Config\ValueType::match
     */
    public function testMatchWithInvalidConfigurable()
    {
        $form = $this->formFactory->create(Form::class, null, array('configurable' => 'type'));

        $this->assertFalse($this->matcher->match(new FormView(array('form_object' => $form)), array(Block::class)));
    }
}
