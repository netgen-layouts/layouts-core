<?php

namespace Netgen\BlockManager\View;

use Symfony\Component\HttpFoundation\Response;

interface ViewInterface
{
    const CONTEXT_DEFAULT = 'default';

    const CONTEXT_ADMIN = 'admin';

    const CONTEXT_API = 'api';

    /**
     * Returns the view identifier.
     *
     * @return string
     */
    public function getIdentifier();

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
     * Returns the view fallback context.
     *
     * @return string|null
     */
    public function getFallbackContext();

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
     * Returns the response which will be used to render the view.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse();

    /**
     * Sets the response which will be used to render the view.
     *
     * @param \Symfony\Component\HttpFoundation\Response
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
     * @throws \OutOfBoundsException If view does not have the parameter
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
