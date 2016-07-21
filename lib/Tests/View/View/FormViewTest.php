<?php

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\View\View\FormView;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormView as SymfonyFormView;
use Symfony\Component\Form\Forms;
use PHPUnit\Framework\TestCase;

class FormViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    protected $form;

    /**
     * @var \Netgen\BlockManager\View\View\FormViewInterface
     */
    protected $view;

    public function setUp()
    {
        $formFactory = Forms::createFormFactoryBuilder()
            ->getFormFactory();

        $this->form = $formFactory->create(FormType::class);

        $this->view = new FormView($this->form);
        $this->view->addParameters(array('param' => 'value'));
        $this->view->addParameters(array('form' => 42));
    }

    /**
     * @covers \Netgen\BlockManager\View\View\FormView::__construct
     * @covers \Netgen\BlockManager\View\View\FormView::getForm
     * @covers \Netgen\BlockManager\View\View\FormView::getFormType
     * @covers \Netgen\BlockManager\View\View\FormView::getFormView
     */
    public function testGetForm()
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
    public function testGetIdentifier()
    {
        $this->assertEquals('form_view', $this->view->getIdentifier());
    }
}
