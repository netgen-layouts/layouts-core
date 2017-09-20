<?php

namespace Netgen\BlockManager\View\Twig;

use Exception;
use Twig\Template;

/**
 * Wrapper around a Twig template with a context included (all variables
 * available inside the template).
 */
class ContextualizedTwigTemplate
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
     * Constructor.
     *
     * @param \Twig\Template $template
     * @param array $context
     * @param array $blocks
     */
    public function __construct(Template $template, array $context = array(), array $blocks = array())
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
     * @throws \Exception
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
        } catch (Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        }

        return ob_get_clean();
    }
}
