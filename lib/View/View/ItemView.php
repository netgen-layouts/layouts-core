<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\View\View;

final class ItemView extends View implements ItemViewInterface
{
    public string $identifier {
        get => 'item';
    }

    public CmsItemInterface $item {
        get => $this->getParameter('item');
    }

    public string $viewType {
        get => $this->getParameter('view_type');
    }

    public function __construct(CmsItemInterface $item, string $viewType)
    {
        $this
            ->addInternalParameter('item', $item)
            ->addInternalParameter('view_type', $viewType);
    }
}
