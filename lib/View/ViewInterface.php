<?php

namespace Netgen\BlockManager\View;

use Symfony\Component\HttpFoundation\Response;

interface ViewInterface
{
    /**
     * Context used to render the values in the frontend.
     */
    const CONTEXT_DEFAULT = 'default';

    /**
     * Context used to render the values (mostly blocks) via AJAX based controller.
     */
    const CONTEXT_AJAX = 'ajax';

    /**
     * Context used to render the values in administration interface.
     */
    const CONTEXT_ADMIN = 'admin';

    /**
     * Context used to render the values in the REST API.
     */
    const CONTEXT_API = 'api';

    /**
     * Returns the view identifier.
     *
     * @return string
     */
    public function getIdentifier();

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
     * Returns the view fallback context.
     *
     * Fallback context will be used if no match rules
     * for the original context could be found.
     *
     * @return string|null
     */
    public function getFallbackContext();

    /**
     * Sets the view fallback context.
     *
     * Fallback context will be used if no match rules
     * for the original context could be found.
     *
     * @param string $fallbackContext
     */
    public function setFallbackContext($fallbackContext);

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
     * Returns the response which will be used to render the view.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse();

    /**
     * Sets the response which will be used to render the view.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function setResponse(Response $response);

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
     * @throws \Netgen\BlockManager\Exception\View\ViewException If view does not have the parameter
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
     * Adds a parameter to the view.
     *
     * @param string $parameterName
     * @param mixed $parameterValue
     */
    public function addParameter($parameterName, $parameterValue);

    /**
     * Adds parameters to the view.
     *
     * @param array $parameters
     */
    public function addParameters(array $parameters = []);
}
