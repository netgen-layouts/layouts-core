<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\View\View;

final class PlaceholderView extends View implements PlaceholderViewInterface
{
    public string $identifier {
        get => 'placeholder';
    }

    public Placeholder $placeholder {
        get => $this->getParameter('placeholder');
    }

    public Block $block {
        get => $this->getParameter('block');
    }

    public function __construct(Placeholder $placeholder, Block $block)
    {
        $this
            ->addInternalParameter('placeholder', $placeholder)
            ->addInternalParameter('block', $block);
    }
}
