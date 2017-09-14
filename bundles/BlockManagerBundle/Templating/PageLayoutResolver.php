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

    public function resolvePageLayout()
    {
        return $this->pageLayout;
    }
}
