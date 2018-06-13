<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Twig;

use Throwable;
use Twig\Template;

/**
 * Wrapper around a Twig template with a context included (all variables
 * available inside the template).
 */
final class ContextualizedTwigTemplate
{
    /**
     * @var \Twig\Template
     */
    private $template;

    /**
     * @var array
     */
    private $context;

    /**
     * @var array
     */
    private $blocks;

    /**
     * @param \Twig\Template $template
     * @param array $context
     * @param array $blocks
     */
    public function __construct(Template $template, array $context = [], array $blocks = [])
    {
        $this->template = $template;
        $this->context = $context;
        $this->blocks = $blocks;
    }

    /**
     * Renders the provided block. If block does not exist, an empty string will be returned.
     *
     * @param string $blockName
     *
     * @throws \Throwable
     *
     * @return string
     */
    public function renderBlock($blockName)
    {
        if (!$this->template->hasBlock($blockName, $this->context, $this->blocks)) {
            return '';
        }

        $level = ob_get_level();
        ob_start();

        try {
            $this->template->displayBlock($blockName, $this->context, $this->blocks);
        } catch (Throwable $t) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $t;
        }

        return (string) ob_get_clean();
    }
}
