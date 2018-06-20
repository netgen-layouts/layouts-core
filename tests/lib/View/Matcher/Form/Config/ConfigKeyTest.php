<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Form\Config;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Matcher\Stubs\Form;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Form\Config\ConfigKey;
use Netgen\BlockManager\View\View\FormView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Forms;

final class ConfigKeyTest extends TestCase
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

        $this->matcher = new ConfigKey();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Config\ConfigKey::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'config_key' => 'test',
            ]
        );

        $this->assertSame($expected, $this->matcher->match(new FormView($form), $config));
    }

    public function matchProvider(): array
    {
        return [
            [[], false],
            [['other'], false],
            [['test'], true],
            [['other', 'other2'], false],
            [['test', 'other'], true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Config\ConfigKey::match
     */
    public function testMatchWithNoFormView(): void
    {
        $this->assertFalse($this->matcher->match(new View(new Value()), []));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Config\ConfigKey::match
     */
    public function testMatchWithNoConfigurable(): void
    {
        $form = $this->formFactory->create(Form::class);

        $this->assertFalse($this->matcher->match(new FormView($form), ['test']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Form\Config\ConfigKey::match
     */
    public function testMatchWithInvalidConfigurable(): void
    {
        $form = $this->formFactory->create(Form::class, null, ['config_key' => 'type']);

        $this->assertFalse($this->matcher->match(new FormView($form), ['test']));
    }
}
