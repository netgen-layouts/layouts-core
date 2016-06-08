<?php

namespace Netgen\Bundle\BlockManagerUIBundle\Controller\Admin;

use Netgen\Bundle\BlockManagerBundle\Controller\Controller;

class LayoutResolverController extends Controller
{
    public function index()
    {
        return $this->render(
            'NetgenBlockManagerUIBundle:admin/layout_resolver:index.html.twig'
        );
    }
}
