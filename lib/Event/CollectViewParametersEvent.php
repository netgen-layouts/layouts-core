<?php

namespace Netgen\BlockManager\Event;

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
     * Parameter bag used to manipulate the view parameters. Its contents will be injected to the view.
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameterBag;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     */
    public function __construct(ViewInterface $view)
    {
        $this->view = clone $view;
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
