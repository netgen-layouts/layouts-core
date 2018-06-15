<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig;

final class GlobalVariable
{
    /**
     * @var string|null
     */
    private $pageLayoutTemplate;

    /**
     * Sets the pagelayout template.
     */
    public function setPageLayoutTemplate(string $pageLayoutTemplate): void
    {
        $this->pageLayoutTemplate = $pageLayoutTemplate;
    }

    /**
     * Returns the pagelayout template or null if no pagelayout template exists.
     */
    public function getPageLayoutTemplate(): ?string
    {
        return $this->pageLayoutTemplate;
    }
}
