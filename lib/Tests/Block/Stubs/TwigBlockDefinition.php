<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

class TwigBlockDefinition extends BlockDefinition
{
    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    public function getHandler()
    {
        return new TwigBlockDefinitionHandler();
    }
}
