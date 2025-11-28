<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Netgen\Layouts\View\View;

final class LayoutTypeView extends View implements LayoutTypeViewInterface
{
    public string $identifier {
        get => 'layout';
    }

    public LayoutTypeInterface $layoutType {
        get => $this->getParameter('layout_type');
    }

    public function __construct(LayoutTypeInterface $layoutType)
    {
        $this->addInternalParameter('layout_type', $layoutType);
    }
}
