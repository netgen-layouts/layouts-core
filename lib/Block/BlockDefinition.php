<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;

final class BlockDefinition extends AbstractBlockDefinition
{
    public private(set) BlockDefinitionHandlerInterface $handler;
}
