<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Netgen\Layouts\View\View;

final class LayoutTypeView extends View implements LayoutTypeViewInterface
{
    public function __construct(LayoutTypeInterface $layoutType)
    {
        $this->parameters['layout_type'] = $layoutType;
    }

    public function getLayoutType(): LayoutTypeInterface
    {
        return $this->parameters['layout_type'];
    }

    public static function getIdentifier(): string
    {
        return 'layout';
    }
}
