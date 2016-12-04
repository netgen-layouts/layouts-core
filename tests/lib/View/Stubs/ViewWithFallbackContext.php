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
     * Constructor.
     *
     * @param string $fallbackContext
     * @param array $parameters
     */
    public function __construct($fallbackContext, array $parameters = array())
    {
        $this->fallbackContext = $fallbackContext;

        parent::__construct($parameters);
    }

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
