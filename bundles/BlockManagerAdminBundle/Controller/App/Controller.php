<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App;

use Netgen\Bundle\BlockManagerBundle\Controller\Controller as BaseController;

abstract class Controller extends BaseController
{
    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_EDITOR');
    }
}
