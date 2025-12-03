<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\View\View;

final class BlockView extends View implements BlockViewInterface
{
    public string $identifier {
        get => 'block';
    }

    public function __construct(
        public private(set) Block $block,
    ) {
        $this->addInternalParameter('block', $this->block);
    }
}
