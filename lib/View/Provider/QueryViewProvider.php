<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\View\QueryView;

class QueryViewProvider implements ViewProviderInterface
{
    /**
     * Provides the view.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView(Value $value)
    {
        /** @var \Netgen\BlockManager\API\Values\Collection\Query $value */
        $queryView = new QueryView();
        $queryView->setQuery($value);

        return $queryView;
    }

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     *
     * @return bool
     */
    public function supports(Value $value)
    {
        return $value instanceof Query;
    }
}
