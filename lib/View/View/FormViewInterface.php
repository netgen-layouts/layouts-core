<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\Form\FormInterface;

interface FormViewInterface extends ViewInterface
{
    /**
     * Returns the form.
     */
    public FormInterface $form { get; }

    /**
     * Returns the form type.
     */
    public string $formType { get; }
}
