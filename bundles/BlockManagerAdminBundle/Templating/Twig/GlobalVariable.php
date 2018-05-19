<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig;

final class GlobalVariable
{
    /**
     * @var string|null
     */
    private $pageLayoutTemplate;

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
     * Returns the pagelayout template or null if no pagelayout template exists.
     *
     * @return string|null
     */
    public function getPageLayoutTemplate()
    {
        return $this->pageLayoutTemplate;
    }
}
