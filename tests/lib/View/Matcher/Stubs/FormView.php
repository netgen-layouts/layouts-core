<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Stubs;

use Netgen\BlockManager\View\View;
use Netgen\BlockManager\View\View\FormViewInterface;

final class FormView extends View implements FormViewInterface
{
    public function getForm()
    {
    }

    public function getFormType()
    {
        return 'form_type';
    }

    public function getFormView()
    {
    }

    public function getIdentifier()
    {
        return 'form_view';
    }
}
