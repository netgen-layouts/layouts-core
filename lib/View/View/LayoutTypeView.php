<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\Layout\Type\LayoutTypeInterface;
use Netgen\BlockManager\View\View;

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
