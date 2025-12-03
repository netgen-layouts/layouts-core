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

    public function __construct(
        public private(set) Placeholder $placeholder,
        public private(set) Block $block,
    ) {
        $this
            ->addInternalParameter('placeholder', $this->placeholder)
            ->addInternalParameter('block', $this->block);
    }
}
