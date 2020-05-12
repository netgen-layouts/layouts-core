<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

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
     * Context used to render the values in the layout editing app.
     */
    public const CONTEXT_APP = 'app';

    /**
     * Returns the view identifier.
     */
    public static function getIdentifier(): string;

    /**
     * Returns the view context.
     */
    public function getContext(): ?string;

    /**
     * Sets the view context.
     */
    public function setContext(string $context): void;

    /**
     * Returns the view fallback context.
     *
     * Fallback context will be used if no match rules
     * for the original context could be found.
     */
    public function getFallbackContext(): ?string;

    /**
     * Sets the view fallback context.
     *
     * Fallback context will be used if no match rules
     * for the original context could be found.
     */
    public function setFallbackContext(string $fallbackContext): void;

    /**
     * Returns the view template or null if template does not exist in the view.
     */
    public function getTemplate(): ?string;

    /**
     * Sets the view template.
     */
    public function setTemplate(string $template): void;

    /**
     * Returns the response which will be used to render the view
     * or null if no response has been set.
     */
    public function getResponse(): ?Response;

    /**
     * Sets the response which will be used to render the view.
     */
    public function setResponse(Response $response): void;

    /**
     * Returns if the view has a parameter.
     */
    public function hasParameter(string $identifier): bool;

    /**
     * Returns the view parameter by identifier.
     *
     * @throws \Netgen\Layouts\Exception\View\ViewException If view does not have the parameter
     *
     * @return mixed
     */
    public function getParameter(string $identifier);

    /**
     * Returns the view parameters.
     *
     * @return array<string, mixed>
     */
    public function getParameters(): array;

    /**
     * Adds a parameter to the view.
     *
     * @param mixed $parameterValue
     */
    public function addParameter(string $parameterName, $parameterValue): void;

    /**
     * Adds parameters to the view.
     *
     * @param array<string, mixed> $parameters
     */
    public function addParameters(array $parameters): void;
}
