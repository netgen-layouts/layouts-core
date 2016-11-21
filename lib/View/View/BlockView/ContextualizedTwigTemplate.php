<?php

namespace Netgen\BlockManager\View\View\BlockView;

use Twig_Template;
use Exception;

class ContextualizedTwigTemplate
{
    /**
     * @var \Twig_Template
     */
    protected $template;

    /**
     * @var array
     */
    protected $context;

    /**
     * @var array
     */
    protected $blocks;

    /**
     * Constructor.
     *
     * @param \Twig_Template $template
     * @param array $context
     * @param array $blocks
     */
    public function __construct(Twig_Template $template, array $context = array(), array $blocks = array())
    {
        $this->template = $template;
        $this->context = $context;
        $this->blocks = $blocks;
    }

    /**
     * Renders the provided block.
     *
     * @param string $blockName
     *
     * @throws \Exception
     *
     * @return string
     */
    public function renderBlock($blockName)
    {
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
