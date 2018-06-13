<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\Bundle\BlockManagerBundle\Controller\Controller as BaseController;

abstract class Controller extends BaseController
{
    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_API');
    }
}
