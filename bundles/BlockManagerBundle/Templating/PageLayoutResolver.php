<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating;

class PageLayoutResolver implements PageLayoutResolverInterface
{
    /**
     * @var string
     */
    protected $pageLayout;

    /**
     * Constructor.
     *
     * @param string $pageLayout
     */
    public function __construct($pageLayout)
    {
        $this->pageLayout = $pageLayout;
    }

    /**
     * Resolves the main page layout used to render the page.
     *
     * @return string
     */
    public function resolvePageLayout()
    {
        return $this->pageLayout;
    }
}
