<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating;

interface PageLayoutResolverInterface
{
    /**
     * Resolves the main page layout used to render the page.
     *
     * @return string
     */
    public function resolvePageLayout();
}
