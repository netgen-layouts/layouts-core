<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\View\Provider\FormViewProvider;
use Netgen\BlockManager\View\View\FormViewInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class FormViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    private $formViewProvider;

    public function setUp(): void
    {
        $this->formViewProvider = new FormViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\FormViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $formView = new FormView();
        $form = $this->createMock(FormInterface::class);
        $form->expects(self::once())
            ->method('createView')
            ->will(self::returnValue($formView));

        $view = $this->formViewProvider->provideView($form);

        self::assertInstanceOf(FormViewInterface::class, $view);

        self::assertSame($form, $view->getForm());
        self::assertNull($view->getTemplate());
        self::assertSame(
            [
                'form_object' => $form,
                'form' => $formView,
            ],
            $view->getParameters()
        );
    }

    /**
     * @param mixed $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\FormViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, bool $supports): void
    {
        self::assertSame($supports, $this->formViewProvider->supports($value));
    }

    public function supportsProvider(): array
    {
        return [
            [new Value(), false],
            [$this->createMock(FormInterface::class), true],
            [new Layout(), false],
        ];
    }
}
