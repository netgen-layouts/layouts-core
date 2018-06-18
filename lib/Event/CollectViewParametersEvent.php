<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Event;

use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event object is used for build_view and render_view events.
 * It allows modification of template parameters before they are passed
 * to the template.
 */
final class CollectViewParametersEvent extends Event
{
    /**
     * Returns the view object that is being built.
     *
     * @var \Netgen\BlockManager\View\ViewInterface
     */
    private $view;

    /**
     * Parameters to be injected into the view.
     *
     * @var array
     */
    private $parameters = [];

    public function __construct(ViewInterface $view)
    {
        $this->view = $view;
    }

    /**
     * Returns the parameters that will be injected into the View.
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Adds the parameter to the view.
     *
     * @param string $parameterName
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
