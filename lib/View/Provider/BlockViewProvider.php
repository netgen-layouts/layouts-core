<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\View\BlockView;

class BlockViewProvider implements ViewProviderInterface
{
    /**
     * Provides the view.
     *
     * @param mixed $value
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView($value)
    {
        /** @var \Netgen\BlockManager\API\Values\Page\Block $value */
        $blockView = new BlockView($value);

        return $blockView;
    }

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function supports($value)
    {
        return $value instanceof Block;
    }
}
