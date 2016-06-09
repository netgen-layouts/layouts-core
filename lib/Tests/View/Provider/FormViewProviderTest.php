<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\View\Provider\FormViewProvider;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\FormViewInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class FormViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    protected $formViewProvider;

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

        /** @var \Netgen\BlockManager\View\FormViewInterface $view */
        $view = $this->formViewProvider->provideView($form);

        self::assertInstanceOf(FormViewInterface::class, $view);

        self::assertEquals($form, $view->getForm());
        self::assertNull($view->getTemplate());
        self::assertEquals(
            array(
                'form' => $formView,
            ),
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
        self::assertEquals($supports, $this->formViewProvider->supports($value));
    }

    /**
     * Provider for {@link self::testSupports}.
     *
     * @return array
     */
    public function supportsProvider()
    {
        return array(
            array(new Value(), false),
            array($this->createMock(FormInterface::class), true),
            array(new Layout(), false),
        );
    }
}
