<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;

/**
 * @final
 */
class TwigBlockDefinition extends AbstractBlockDefinition implements TwigBlockDefinitionInterface
{
    /**
     * @var \Netgen\Layouts\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface
     */
    private $handler;

    public function getTwigBlockNames(Block $block): array
    {
        return $this->handler->getTwigBlockNames($block);
    }

    protected function getHandler(): BlockDefinitionHandlerInterface
    {
        return $this->handler;
    }
}
