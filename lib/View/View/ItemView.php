<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\View\View;

final class ItemView extends View implements ItemViewInterface
{
    public function __construct(CmsItemInterface $item, string $viewType)
    {
        $this->parameters['item'] = $item;
        $this->parameters['view_type'] = $viewType;
    }

    public function getItem(): CmsItemInterface
    {
        return $this->parameters['item'];
    }

    public function getViewType(): string
    {
        return $this->parameters['view_type'];
    }

    public static function getIdentifier(): string
    {
        return 'item';
    }
}
