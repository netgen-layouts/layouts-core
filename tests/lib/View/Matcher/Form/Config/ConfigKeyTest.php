<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Form\Config;

use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Matcher\Stubs\Form;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Form\Config\ConfigKey;
use Netgen\Layouts\View\View\FormView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

final class ConfigKeyTest extends TestCase
{
    private FormFactoryInterface $formFactory;

    private ConfigKey $matcher;

    protected function setUp(): void
    {
        $this->formFactory = Forms::createFormFactoryBuilder()
            ->getFormFactory();

        $this->matcher = new ConfigKey();
    }

    /**
     * @param mixed[] $config
     *
     * @covers \Netgen\Layouts\View\Matcher\Form\Config\ConfigKey::match
     *
     * @dataProvider matchDataProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $form = $this->formFactory->create(
            Form::class,
            null,
            [
                'config_key' => 'test',
            ],
        );

        self::assertSame($expected, $this->matcher->match(new FormView($form), $config));
    }

    public static function matchDataProvider(): iterable
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
     * @covers \Netgen\Layouts\View\Matcher\Form\Config\ConfigKey::match
     */
    public function testMatchWithNoFormView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Form\Config\ConfigKey::match
     */
    public function testMatchWithNoConfigurable(): void
    {
        $form = $this->formFactory->create(Form::class);

        self::assertFalse($this->matcher->match(new FormView($form), ['test']));
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Form\Config\ConfigKey::match
     */
    public function testMatchWithInvalidConfigurable(): void
    {
        $form = $this->formFactory->create(Form::class, null, ['config_key' => 'type']);

        self::assertFalse($this->matcher->match(new FormView($form), ['test']));
    }
}
