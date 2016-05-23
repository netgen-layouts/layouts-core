<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\View\QueryView;

class QueryViewProvider implements ViewProviderInterface
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
        /** @var \Netgen\BlockManager\API\Values\Collection\Query $value */
        $queryView = new QueryView($value);

        return $queryView;
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
        return $value instanceof Query;
    }
}
