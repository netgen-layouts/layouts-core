<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig;

class GlobalVariable
{
    /**
     * @var string
     */
    protected $pageLayoutTemplate;

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
