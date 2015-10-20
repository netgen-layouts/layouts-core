<?php

namespace Netgen\BlockManager\View;

interface ViewInterface
{
    /**
     * Returns the view context.
     *
     * @return string
     */
    public function getContext();

    /**
     * Sets the view context.
     *
     * @param string $context
     */
    public function setContext($context);

    /**
     * Returns the view template.
     *
     * @return string
     */
    public function getTemplate();

    /**
     * Sets the view template.
     *
     * @param string $template
     */
    public function setTemplate($template);

    /**
     * Returns the view parameters.
     *
     * @return array
     */
    public function getParameters();

    /**
     * Sets the view parameters.
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters = array());
}
