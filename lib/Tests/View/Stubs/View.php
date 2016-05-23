<?php

namespace Netgen\BlockManager\Tests\View\Stubs;

use Netgen\BlockManager\View\View as BaseView;

class View extends BaseView
{
    /**
     * Constructor.
     *
     * @param mixed $valueObject
     */
    public function __construct($valueObject)
    {
        $this->valueObject = $valueObject;
    }

    /**
     * Returns the view alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return 'view';
    }
}
