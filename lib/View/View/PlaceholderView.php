<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\View\View;

final class PlaceholderView extends View implements PlaceholderViewInterface
{
    public function __construct(Placeholder $placeholder, Block $block)
    {
        $this->parameters['placeholder'] = $placeholder;
        $this->parameters['block'] = $block;
    }

    public function getPlaceholder(): Placeholder
    {
        return $this->parameters['placeholder'];
    }

    public function getBlock(): Block
    {
        return $this->parameters['block'];
    }

    public static function getIdentifier(): string
    {
        return 'placeholder';
    }
}
