<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\View\Provider\FormViewProvider;
use Netgen\Layouts\View\View\FormViewInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class FormViewProviderTest extends TestCase
{
    private FormViewProvider $formViewProvider;

    protected function setUp(): void
    {
        $this->formViewProvider = new FormViewProvider();
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\FormViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $formView = new FormView();
        $form = $this->createMock(FormInterface::class);
        $form->expects(self::once())
            ->method('createView')
            ->willReturn($formView);

        $view = $this->formViewProvider->provideView($form);

        self::assertInstanceOf(FormViewInterface::class, $view);

        self::assertSame($form, $view->getForm());
        self::assertNull($view->getTemplate());
        self::assertSame(
            [
                'form_object' => $form,
                'form' => $formView,
            ],
            $view->getParameters(),
        );
    }

    /**
     * @param class-string $value
     *
     * @covers \Netgen\Layouts\View\Provider\FormViewProvider::supports
     *
     * @dataProvider supportsDataProvider
     */
    public function testSupports($value, bool $supports): void
    {
        self::assertSame($supports, $this->formViewProvider->supports($this->createMock($value)));
    }

    public static function supportsDataProvider(): iterable
    {
        return [
            [stdClass::class, false],
            [FormInterface::class, true],
        ];
    }
}
