<?php

namespace Netgen\BlockManager\View\View\BlockView;

use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\API\Values\Page\Block;

class TwigBlockView extends BlockView
{
    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param string $twigBlockContent
     */
    public function __construct(Block $block, $twigBlockContent)
    {
        parent::__construct($block);

        $this->internalParameters['twig_block_content'] = $twigBlockContent;
    }
}
