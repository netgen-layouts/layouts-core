<?php

namespace Netgen\BlockManager\Event\View;

use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\ParameterBag;

class CollectViewParametersEvent extends Event
{
    /**
     * Copy of the view object that is being built.
     *
     * @var \Netgen\BlockManager\View\ViewInterface
     */
    protected $view;

    /**
     * Parameters that were provided to the view builder.
     *
     * @var array
     */
    protected $builderParameters;

    /**
     * Parameter bag used to manipulate the view parameters. Its contents will be injected to the view.
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameterBag;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     * @param array $builderParameters
     */
    public function __construct(ViewInterface $view, array $builderParameters)
    {
        $this->view = clone $view;
        $this->builderParameters = $builderParameters;
        $this->parameterBag = new ParameterBag();
    }

    /**
     * Returns the parameters that can be injected into the View.
     *
     * @return array
     */
    public function getViewParameters()
    {
        return $this->parameterBag->all();
    }

    /**
     * Returns the parameters that were passed to the view builder.
     *
     * @return array
     */
    public function getBuilderParameters()
    {
        return $this->builderParameters;
    }

    /**
     * Returns the parameter bag used to manipulate the view parameters.
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function getParameterBag()
    {
        return $this->parameterBag;
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
