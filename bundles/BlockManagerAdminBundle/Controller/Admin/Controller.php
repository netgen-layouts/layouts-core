<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin;

use Netgen\Bundle\BlockManagerBundle\Controller\Controller as BaseController;

abstract class Controller extends BaseController
{
    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_ADMIN');
    }
}
