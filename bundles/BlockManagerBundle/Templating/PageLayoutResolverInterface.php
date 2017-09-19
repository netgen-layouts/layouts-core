<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating;

/**
 * Page layout resolvers are used to programmatically define which
 * pagelayout will be used to render the page.
 */
interface PageLayoutResolverInterface
{
    /**
     * Resolves the main page layout used to render the page.
     *
     * @return string
     */
    public function resolvePageLayout();
}
