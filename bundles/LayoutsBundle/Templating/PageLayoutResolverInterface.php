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
     *
     * @throws \Netgen\Layouts\Exception\RuntimeException if the resolved page layout is empty
     */
    public function resolvePageLayout(): string;
}
