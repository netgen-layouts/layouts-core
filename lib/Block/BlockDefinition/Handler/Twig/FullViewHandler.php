<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler\Twig;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;

/**
 * Block used to render the full view (or rather, the result of the controller).
 *
 * The default block name to use & render is provided by the constructor.
 */
class FullViewHandler extends BlockDefinitionHandler implements TwigBlockDefinitionHandlerInterface
{
    /**
     * @var string
     */
    private $twigBlockName;

    /**
     * Constructor.
     *
     * @param string $twigBlockName
     */
    public function __construct($twigBlockName)
    {
        $this->twigBlockName = $twigBlockName;
    }

    public function isContextual(Block $block)
    {
        return true;
    }

    public function getTwigBlockName(Block $block)
    {
        return $this->twigBlockName;
    }
}
