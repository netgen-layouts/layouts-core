<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\View\View\FormView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormView as SymfonyFormView;

final class FormViewTest extends TestCase
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    private $form;

    /**
     * @var \Netgen\BlockManager\View\View\FormViewInterface
     */
    private $view;

    public function setUp(): void
    {
        $formFactory = Forms::createFormFactoryBuilder()
            ->getFormFactory();

        $this->form = $formFactory->create(FormType::class);

        $this->view = new FormView(
            [
                'form_object' => $this->form,
                'form' => $this->form->createView(),
            ]
        );

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('form', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\FormView::__construct
     * @covers \Netgen\BlockManager\View\View\FormView::getForm
     * @covers \Netgen\BlockManager\View\View\FormView::getFormType
     * @covers \Netgen\BlockManager\View\View\FormView::getFormView
     */
    public function testGetForm(): void
    {
        $this->assertEquals($this->form, $this->view->getForm());
        $this->assertEquals(FormType::class, $this->view->getFormType());
        $this->assertInstanceOf(SymfonyFormView::class, $this->view->getFormView());

        $this->assertEquals('value', $this->view->getParameter('param'));
        $this->assertInstanceOf(SymfonyFormView::class, $this->view->getParameter('form'));
    }

    /**
     * @covers \Netgen\BlockManager\View\View\FormView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertEquals('form_view', $this->view->getIdentifier());
    }
}
