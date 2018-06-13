<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
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

    public function setUp()
    {
        $this->formViewProvider = new FormViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\FormViewProvider::provideView
     */
    public function testProvideView()
    {
        $formView = new FormView();
        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())
            ->method('createView')
            ->will($this->returnValue($formView));

        /** @var \Netgen\BlockManager\View\View\FormViewInterface $view */
        $view = $this->formViewProvider->provideView($form);

        $this->assertInstanceOf(FormViewInterface::class, $view);

        $this->assertEquals($form, $view->getForm());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            [
                'form_object' => $form,
                'form' => $formView,
            ],
            $view->getParameters()
        );
    }

    /**
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\FormViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, $supports)
    {
        $this->assertEquals($supports, $this->formViewProvider->supports($value));
    }

    /**
     * Provider for {@link self::testSupports}.
     *
     * @return array
     */
    public function supportsProvider()
    {
        return [
            [new Value(), false],
            [$this->createMock(FormInterface::class), true],
            [new Layout(), false],
        ];
    }
}
