<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\View\View;
use Symfony\Component\Form\FormInterface;

final class FormView extends View implements FormViewInterface
{
    public string $identifier {
        get => 'form';
    }

    public string $formType {
        get => $this->form->getConfig()->getType()->getInnerType()::class;
    }

    public function __construct(
        public private(set) FormInterface $form,
    ) {
        $this
            ->addInternalParameter('form_object', $this->form)
            ->addInternalParameter('form', $this->form->createView());
    }
}
