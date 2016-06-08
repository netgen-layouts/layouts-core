<?php

namespace Netgen\Bundle\BlockManagerAdminUIBundle\Controller\Admin;

use Netgen\Bundle\BlockManagerBundle\Controller\Controller;

class LayoutResolverController extends Controller
{
    public function index()
    {
        return $this->render(
            'NetgenBlockManagerAdminUIBundle:admin/layout_resolver:index.html.twig'
        );
    }
}
