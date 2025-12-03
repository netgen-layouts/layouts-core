<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\View\View\FormView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;

#[CoversClass(FormView::class)]
final class FormViewTest extends TestCase
{
    private FormInterface $form;

    private FormView $view;

    protected function setUp(): void
    {
        $formFactory = Forms::createFormFactoryBuilder()
            ->getFormFactory();

        $this->form = $formFactory->create();

        $this->view = new FormView($this->form);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('form', 42);
    }

    public function testGetForm(): void
    {
        self::assertSame($this->form, $this->view->form);
        self::assertSame(FormType::class, $this->view->formType);

        self::assertSame('value', $this->view->getParameter('param'));
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('form', $this->view->identifier);
    }
}
