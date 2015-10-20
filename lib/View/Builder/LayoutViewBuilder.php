<?php

namespace Netgen\BlockManager\View\Builder;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\View\LayoutView;
use InvalidArgumentException;

class LayoutViewBuilder implements ViewBuilder
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
        if (!$value instanceof Layout) {
            throw new InvalidArgumentException('Layout view builder accepts only Layout value objects to build from');
        }

        $layoutView = new LayoutView();
        $layoutView->setLayout($value);
        $layoutView->setContext($context);

        $parameters['layout'] = $value;
        $layoutView->setParameters($parameters);

        return $layoutView;
    }
}
