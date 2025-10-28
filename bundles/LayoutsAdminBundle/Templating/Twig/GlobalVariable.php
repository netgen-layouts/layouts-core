<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Templating\Twig;

final class GlobalVariable
{
    public function __construct(
        private string $pageLayoutTemplate,
    ) {}

    /**
     * Sets the pagelayout template.
     */
    public function setPageLayoutTemplate(string $pageLayoutTemplate): void
    {
        $this->pageLayoutTemplate = $pageLayoutTemplate;
    }

    /**
     * Returns the pagelayout template.
     */
    public function getPageLayoutTemplate(): string
    {
        return $this->pageLayoutTemplate;
    }
}
