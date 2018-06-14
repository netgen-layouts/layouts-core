<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View;

use Symfony\Component\HttpFoundation\Response;

interface ViewInterface
{
    /**
     * Context used to render the values in the frontend.
     */
    public const CONTEXT_DEFAULT = 'default';

    /**
     * Context used to render the values (mostly blocks) via AJAX based controller.
     */
    public const CONTEXT_AJAX = 'ajax';

    /**
     * Context used to render the values in administration interface.
     */
    public const CONTEXT_ADMIN = 'admin';

    /**
     * Context used to render the values in the REST API.
     */
    public const CONTEXT_API = 'api';

    /**
     * Returns the view identifier.
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Returns the view context.
     *
     * @return string|null
     */
    public function getContext(): ?string;

    /**
     * Sets the view context.
     *
     * @param string $context
     */
    public function setContext(string $context): void;

    /**
     * Returns the view fallback context.
     *
     * Fallback context will be used if no match rules
     * for the original context could be found.
     *
     * @return string|null
     */
    public function getFallbackContext(): ?string;

    /**
     * Sets the view fallback context.
     *
     * Fallback context will be used if no match rules
     * for the original context could be found.
     *
     * @param string $fallbackContext
     */
    public function setFallbackContext(string $fallbackContext): void;

    /**
     * Returns the view template or null if template does not exist in the view.
     *
     * @return string|null
     */
    public function getTemplate(): ?string;

    /**
     * Sets the view template.
     *
     * @param string $template
     */
    public function setTemplate(string $template): void;

    /**
     * Returns the response which will be used to render the view
     * or null if no response has been set.
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function getResponse(): ?Response;

    /**
     * Sets the response which will be used to render the view.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function setResponse(Response $response): void;

    /**
     * Returns if the view has a parameter.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasParameter(string $identifier): bool;

    /**
     * Returns the view parameter by identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\View\ViewException If view does not have the parameter
     *
     * @return mixed
     */
    public function getParameter(string $identifier);

    /**
     * Returns the view parameters.
     *
     * @return array
     */
    public function getParameters(): array;

    /**
     * Adds a parameter to the view.
     *
     * @param string $parameterName
     * @param mixed $parameterValue
     */
    public function addParameter(string $parameterName, $parameterValue): void;

    /**
     * Adds parameters to the view.
     *
     * @param array $parameters
     */
    public function addParameters(array $parameters = []): void;
}
