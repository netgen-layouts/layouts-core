<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating;

use Netgen\Layouts\Exception\RuntimeException;

use function sprintf;

/**
 * This is a default implementation of page layout resolver
 * which just provides the pagelayout specified in the constructor.
 */
final class PageLayoutResolver implements PageLayoutResolverInterface
{
    private string $pageLayout;

    public function __construct(string $pageLayout)
    {
        $this->pageLayout = $pageLayout;
    }

    public function resolvePageLayout(): string
    {
        if ($this->pageLayout === '') {
            throw new RuntimeException(
                sprintf(
                    '%s%s',
                    'Base page layout not specified. To render the page with Netgen Layouts, ',
                    'specify the base page layout with "netgen_layouts.pagelayout" semantic config.',
                ),
            );
        }

        return $this->pageLayout;
    }
}
