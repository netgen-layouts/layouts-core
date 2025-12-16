<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\View\Provider\FormViewProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView as SymfonyFormView;

#[CoversClass(FormViewProvider::class)]
final class FormViewProviderTest extends TestCase
{
    private FormViewProvider $formViewProvider;

    protected function setUp(): void
    {
        $this->formViewProvider = new FormViewProvider();
    }

    public function testProvideView(): void
    {
        $formView = new SymfonyFormView();
        $formStub = self::createStub(FormInterface::class);
        $formStub
            ->method('createView')
            ->willReturn($formView);

        $view = $this->formViewProvider->provideView($formStub);

        self::assertSame($formStub, $view->form);
        self::assertNull($view->template);
        self::assertSame(
            [
                'form_object' => $formStub,
                'form' => $formView,
            ],
            $view->parameters,
        );
    }

    /**
     * @param class-string $value
     */
    #[DataProvider('supportsDataProvider')]
    public function testSupports(string $value, bool $supports): void
    {
        self::assertSame($supports, $this->formViewProvider->supports(self::createStub($value)));
    }

    public static function supportsDataProvider(): iterable
    {
        return [
            [stdClass::class, false],
            [FormInterface::class, true],
        ];
    }
}
