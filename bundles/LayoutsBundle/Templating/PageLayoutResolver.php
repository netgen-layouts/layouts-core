<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating;

/**
 * This is a default implementation of page layout resolver
 * which just provides the pagelayout specified in the constructor.
 */
final class PageLayoutResolver implements PageLayoutResolverInterface
{
    /**
     * @var string
     */
    private $pageLayout;

    public function __construct(string $pageLayout)
    {
        $this->pageLayout = $pageLayout;
    }

    public function resolvePageLayout(): string
    {
        return $this->pageLayout;
    }
}
