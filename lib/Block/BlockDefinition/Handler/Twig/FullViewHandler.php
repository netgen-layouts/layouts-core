<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler\Twig;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;

class FullViewHandler extends BlockDefinitionHandler implements TwigBlockDefinitionHandlerInterface
{
    /**
     * @var string
     */
    protected $twigBlockName;

    /**
     * Constructor.
     *
     * @param string $twigBlockName
     */
    public function __construct($twigBlockName)
    {
        $this->twigBlockName = $twigBlockName;
    }

    /**
     * Returns if the provided block is dependent on a context, i.e. current request.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool
     */
    public function isContextual(Block $block)
    {
        return true;
    }

    /**
     * Returns the name of the Twig block to use.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return string
     */
    public function getTwigBlockName(Block $block)
    {
        return $this->twigBlockName;
    }
}
