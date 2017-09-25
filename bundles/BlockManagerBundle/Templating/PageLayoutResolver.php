<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating;

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

    /**
     * Constructor.
     *
     * @param string $pageLayout
     */
    public function __construct($pageLayout)
    {
        $this->pageLayout = $pageLayout;
    }

    public function resolvePageLayout()
    {
        return $this->pageLayout;
    }
}
