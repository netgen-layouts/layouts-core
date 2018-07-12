<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig;

final class GlobalVariable
{
    /**
     * @var string
     */
    private $pageLayoutTemplate;

    public function __construct(string $defaultTemplate)
    {
        $this->pageLayoutTemplate = $defaultTemplate;
    }

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
