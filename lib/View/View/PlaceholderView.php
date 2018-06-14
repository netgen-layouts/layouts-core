<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\View\View;

final class PlaceholderView extends View implements PlaceholderViewInterface
{
    public function getPlaceholder(): Placeholder
    {
        return $this->parameters['placeholder'];
    }

    public function getBlock(): Block
    {
        return $this->parameters['block'];
    }

    public function getIdentifier(): string
    {
        return 'placeholder_view';
    }
}
