<?php

namespace Netgen\BlockManager\View;

interface ViewInterface
{
    const CONTEXT_VIEW = 'view';

    const CONTEXT_API_VIEW = 'api_view';

    const CONTEXT_ADMIN_VIEW = 'admin_view';

    /**
     * Returns the view alias.
     *
     * @return string
     */
    public function getAlias();

    /**
     * Returns the value in this view.
     *
     * @return mixed
     */
    public function getValueObject();

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
     * Returns if the view has a parameter.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasParameter($identifier);

    /**
     * Returns the view parameter by identifier.
     *
     * @param string $identifier
     *
     * @throws \OutOfBoundsException If view does not have the parameter.
     *
     * @return mixed
     */
    public function getParameter($identifier);

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

    /**
     * Adds parameters to the view.
     *
     * @param array $parameters
     */
    public function addParameters(array $parameters = array());
}
