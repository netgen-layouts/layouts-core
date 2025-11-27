<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;

final class TwigBlockDefinition extends AbstractBlockDefinition implements TwigBlockDefinitionInterface
{
    public private(set) TwigBlockDefinitionHandlerInterface $handler;

    public function getTwigBlockNames(Block $block): array
    {
        return $this->handler->getTwigBlockNames($block);
    }
}
