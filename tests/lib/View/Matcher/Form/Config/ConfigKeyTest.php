<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Form\Config;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Matcher\Stubs\Form;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Form\Config\ConfigKey;
use Netgen\BlockManager\View\View\FormView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Forms;

class ConfigKeyTest extends TestCase
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

        $this->matcher = new ConfigKey();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Form\Config\ConfigKey::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            array(
                'config_key' => 'test',
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
            array(array('other'), false),
            array(array('test'), true),
            array(array('other', 'other2'), false),
            array(array('test', 'other'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Config\ConfigKey::match
     */
    public function testMatchWithNoFormView()
    {
        $this->assertFalse($this->matcher->match(new View(array('value' => new Value())), array()));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Config\ConfigKey::match
     */
    public function testMatchWithNoConfigurable()
    {
        $form = $this->formFactory->create(Form::class);

        $this->assertFalse($this->matcher->match(new FormView(array('form_object' => $form)), array('test')));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Config\ConfigKey::match
     */
    public function testMatchWithInvalidConfigurable()
    {
        $form = $this->formFactory->create(Form::class, null, array('config_key' => 'type'));

        $this->assertFalse($this->matcher->match(new FormView(array('form_object' => $form)), array('test')));
    }
}
