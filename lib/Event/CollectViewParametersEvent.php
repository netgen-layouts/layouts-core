<?php

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
     * Copy of the view object that is being built.
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

    /**
     * @param \Netgen\BlockManager\View\ViewInterface $view
     */
    public function __construct(ViewInterface $view)
    {
        $this->view = clone $view;
    }

    /**
     * Returns the parameters that will be injected into the View.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns the parameters that will be injected into the View.
     *
     * @param string $parameterName
     * @param mixed $parameterValue
     *
     * @return array
     */
    public function addParameter($parameterName, $parameterValue)
    {
        return $this->parameters[$parameterName] = $parameterValue;
    }

    /**
     * Returns the copy of the view object.
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function getView()
    {
        return $this->view;
    }
}
