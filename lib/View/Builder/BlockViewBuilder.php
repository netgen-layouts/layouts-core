<?php

namespace Netgen\BlockManager\View\Builder;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\View\BlockView;
use InvalidArgumentException;

class BlockViewBuilder implements ViewBuilder
{
    /**
     * Builds the view.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param array $parameters
     * @param string $context
     *
     * @throws \InvalidArgumentException If value is of unsupported type
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function buildView(Value $value, array $parameters = array(), $context = 'view')
    {
        if (!$value instanceof Block) {
            throw new InvalidArgumentException('Block view builder accepts only Block value objects to build from');
        }

        $blockView = new BlockView();

        $blockView->setBlock($value);
        $blockView->setContext($context);
        $blockView->setParameters($parameters);

        return $blockView;
    }
}
