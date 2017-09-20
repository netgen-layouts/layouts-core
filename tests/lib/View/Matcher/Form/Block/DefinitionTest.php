<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Form\Block;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Matcher\Stubs\Form;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Form\Block\Definition;
use Netgen\BlockManager\View\View\FormView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Forms;

class DefinitionTest extends TestCase
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

        $this->matcher = new Definition();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Form\Block\Definition::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            array(
                'block' => new Block(
                    array(
                        'definition' => new BlockDefinition('block'),
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
            array(array('other_block'), false),
            array(array('block'), true),
            array(array('other_block', 'second_block'), false),
            array(array('block', 'other_block'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithNoFormView()
    {
        $this->assertFalse($this->matcher->match(new View(array('value' => new Value())), array()));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithNoBlock()
    {
        $form = $this->formFactory->create(Form::class);

        $this->assertFalse($this->matcher->match(new FormView(array('form_object' => $form)), array('block')));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithInvalidBlock()
    {
        $form = $this->formFactory->create(Form::class, null, array('block' => 'block'));

        $this->assertFalse($this->matcher->match(new FormView(array('form_object' => $form)), array('block')));
    }
}
