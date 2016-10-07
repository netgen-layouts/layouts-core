<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig;

class GlobalVariable
{
    /**
     * @var string
     */
    protected $pageLayoutTemplate;

    /**
     * Constructor.
     *
     * @param string $pageLayoutTemplate
     */
    public function __construct($pageLayoutTemplate)
    {
        $this->pageLayoutTemplate = $pageLayoutTemplate;
    }

    /**
     * Sets the pagelayout template.
     *
     * @param string $pageLayoutTemplate
     */
    public function setPageLayoutTemplate($pageLayoutTemplate)
    {
        $this->pageLayoutTemplate = $pageLayoutTemplate;
    }

    /**
     * Returns the pagelayout template.
     *
     * @return string
     */
    public function getPageLayoutTemplate()
    {
        return $this->pageLayoutTemplate;
    }
}
