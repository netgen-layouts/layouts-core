<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView as SymfonyFormView;

interface FormViewInterface extends ViewInterface
{
    /**
     * Returns the form.
     */
    public function getForm(): FormInterface;

    /**
     * Returns the form type.
     */
    public function getFormType(): string;

    /**
     * Returns the form view.
     */
    public function getFormView(): SymfonyFormView;
}
