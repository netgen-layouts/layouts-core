<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Form\Block;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\NullBlockDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Matcher\Stubs\Form;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Form\Block\Definition;
use Netgen\BlockManager\View\View\FormView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Forms;

final class DefinitionTest extends TestCase
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

        $this->matcher = new Definition();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Block\Definition::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'block' => new Block(
                    [
                        'definition' => new BlockDefinition(['identifier' => 'block']),
                    ]
                ),
            ]
        );

        $this->assertSame($expected, $this->matcher->match(new FormView(['form_object' => $form]), $config));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\DefinitionTrait::doMatch
     * @covers \Netgen\BlockManager\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithNullBlockDefinition(): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'block' => new Block(
                    [
                        'definition' => new NullBlockDefinition('definition'),
                    ]
                ),
            ]
        );

        $this->assertTrue($this->matcher->match(new FormView(['form_object' => $form]), ['null']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\DefinitionTrait::doMatch
     * @covers \Netgen\BlockManager\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithNullBlockDefinitionReturnsFalse(): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'block' => new Block(
                    [
                        'definition' => new NullBlockDefinition('definition'),
                    ]
                ),
            ]
        );

        $this->assertFalse($this->matcher->match(new FormView(['form_object' => $form]), ['test']));
    }

    public function matchProvider(): array
    {
        return [
            [[], false],
            [['other_block'], false],
            [['block'], true],
            [['other_block', 'second_block'], false],
            [['block', 'other_block'], true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithNoFormView(): void
    {
        $this->assertFalse($this->matcher->match(new View(['value' => new Value()]), []));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithNoBlock(): void
    {
        $form = $this->formFactory->create(Form::class);

        $this->assertFalse($this->matcher->match(new FormView(['form_object' => $form]), ['block']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Block\Definition::match
     */
    public function testMatchWithInvalidBlock(): void
    {
        $form = $this->formFactory->create(Form::class, null, ['block' => 'block']);

        $this->assertFalse($this->matcher->match(new FormView(['form_object' => $form]), ['block']));
    }
}
