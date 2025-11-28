<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

use Symfony\Component\HttpFoundation\Response;

interface ViewInterface
{
    /**
     * Context used to render the values in the frontend.
     */
    final public const string CONTEXT_DEFAULT = 'default';

    /**
     * Context used to render the values (mostly blocks) via AJAX based controller.
     */
    final public const string CONTEXT_AJAX = 'ajax';

    /**
     * Context used to render the values in administration interface.
     */
    final public const string CONTEXT_ADMIN = 'admin';

    /**
     * Context used to render the values in the layout editing app.
     */
    final public const string CONTEXT_APP = 'app';

    /**
     * Returns the view identifier.
     */
    public string $identifier { get; }

    /**
     * Returns the view context.
     */
    public ?string $context { get; set; }

    /**
     * Returns the view fallback context.
     *
     * Fallback context will be used if no match rules
     * for the original context could be found.
     */
    public ?string $fallbackContext { get; set; }

    /**
     * Returns the view template or null if template does not exist in the view.
     */
    public ?string $template { get; set; }

    /**
     * Returns the response which will be used to render the view
     * or null if no response has been set.
     */
    public ?Response $response { get; set; }

    /**
     * Returns the view parameters.
     *
     * @var array<string, mixed>
     */
    public array $parameters { get; }

    /**
     * Returns if the view has a parameter.
     */
    public function hasParameter(string $identifier): bool;

    /**
     * Returns the view parameter by identifier.
     *
     * @throws \Netgen\Layouts\Exception\View\ViewException If view does not have the parameter
     */
    public function getParameter(string $identifier): mixed;

    /**
     * Adds a parameter to the view.
     */
    public function addParameter(string $parameterName, mixed $parameterValue): static;

    /**
     * Adds parameters to the view.
     *
     * @param array<string, mixed> $parameters
     */
    public function addParameters(array $parameters): static;
}
