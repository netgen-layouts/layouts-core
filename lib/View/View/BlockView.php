<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\View\View;

final class BlockView extends View implements BlockViewInterface
{
    public function __construct(Block $block)
    {
        $this->parameters['block'] = $block;
    }

    public function getBlock(): Block
    {
        return $this->parameters['block'];
    }

    public static function getIdentifier(): string
    {
        return 'block';
    }
}
