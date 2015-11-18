<?php

namespace Netgen\BlockManager\Form\Data;

use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Block;

class UpdateBlockData
{
    /**
     * @var \Netgen\BlockManager\API\Values\Page\Block
     */
    public $block;

    /**
     * @var \Netgen\BlockManager\API\Values\BlockUpdateStruct
     */
    public $updateStruct;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\BlockUpdateStruct $updateStruct
     */
    public function __construct(Block $block, BlockUpdateStruct $updateStruct)
    {
        $this->block = $block;
        $this->updateStruct = $updateStruct;
    }
}
