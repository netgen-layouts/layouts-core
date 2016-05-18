<?php

namespace Netgen\BlockManager\Tests\View\Stubs;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\View\View as BaseView;

class View extends BaseView
{
    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     */
    public function __construct(Value $value)
    {
        $this->value = $value;
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
