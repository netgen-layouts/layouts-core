<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Form;

use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Form\Type as TypeMatcher;
use Netgen\Layouts\View\View\FormView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\Forms;

#[CoversClass(TypeMatcher::class)]
final class TypeTest extends TestCase
{
    private TypeMatcher $matcher;

    protected function setUp(): void
    {
        $this->matcher = new TypeMatcher();
    }

    /**
     * @param mixed[] $config
     */
    #[DataProvider('matchDataProvider')]
    public function testMatch(array $config, bool $expected): void
    {
        $formFactory = Forms::createFormFactoryBuilder()
            ->getFormFactory();

        $view = new FormView($formFactory->create());

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    /**
     * @return iterable<mixed>
     */
    public static function matchDataProvider(): iterable
    {
        return [
            [[], false],
            [[Type\TextType::class], false],
            [[Type\FormType::class], true],
            [[Type\TextType::class, Type\IntegerType::class], false],
            [[Type\FormType::class, Type\TextType::class], true],
        ];
    }

    public function testMatchWithNoFormView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
