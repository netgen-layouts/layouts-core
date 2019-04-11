<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating;

/**
 * Page layout resolvers are used to programmatically define which
 * pagelayout will be used to render the page.
 */
interface PageLayoutResolverInterface
{
    /**
     * Resolves the main page layout used to render the page.
     */
    public function resolvePageLayout(): string;
}
