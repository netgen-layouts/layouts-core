<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\Bundle\BlockManagerBundle\Controller\Controller as BaseController;

abstract class Controller extends BaseController
{
    /**
     * @var int
     */
    protected $maxLimit;

    /**
     * Sets the max limit used by the API.
     *
     * @param int $maxLimit
     */
    public function setMaxLimit($maxLimit)
    {
        $this->maxLimit = $maxLimit;
    }
}
