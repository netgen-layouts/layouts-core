<?php

namespace Netgen\BlockManager\Tests\View\Stubs;

use Netgen\BlockManager\View\View as BaseView;

class ViewWithFallbackContext extends BaseView
{
    /**
     * @var string
     */
    protected $fallbackContext;

    /**
     * Returns the view identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'view';
    }

    /**
     * Returns the view fallback context.
     *
     * @return string|null
     */
    public function getFallbackContext()
    {
        return $this->fallbackContext;
    }
}
