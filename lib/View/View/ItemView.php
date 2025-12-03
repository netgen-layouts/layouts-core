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

    public function __construct(
        public private(set) CmsItemInterface $item,
        public private(set) string $viewType,
    ) {
        $this
            ->addInternalParameter('item', $this->item)
            ->addInternalParameter('view_type', $this->viewType);
    }
}
