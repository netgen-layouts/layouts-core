<?php

declare(strict_types=1);

namespace Netgen\Layouts\Event;

use Netgen\Layouts\Utils\BackwardsCompatibility\Event;
use Netgen\Layouts\View\ViewInterface;

/**
 * This event object is used for build_view and render_view events.
 * It allows modification of template parameters before they are passed
 * to the template.
 */
final class CollectViewParametersEvent extends Event
{
    /**
     * Returns the view object that is being built.
     */
    private ViewInterface $view;

    /**
     * Parameters to be injected into the view.
     *
     * @var array<string, mixed>
     */
    private array $parameters = [];

    public function __construct(ViewInterface $view)
    {
        $this->view = $view;
    }

    /**
     * Returns the parameters that will be injected into the View.
     *
     * @return array<string, mixed>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Adds the parameter to the view.
     *
     * @param mixed $parameterValue
     */
    public function addParameter(string $parameterName, $parameterValue): void
    {
        $this->parameters[$parameterName] = $parameterValue;
    }

    /**
     * Returns the view object.
     */
    public function getView(): ViewInterface
    {
        return $this->view;
    }
}
