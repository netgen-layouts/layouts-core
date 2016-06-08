<?php

namespace Netgen\Bundle\BlockManagerAdminUIBundle\Controller\App;

use Netgen\Bundle\BlockManagerBundle\Controller\Controller;

class AppController extends Controller
{
    public function index()
    {
        return $this->render(
            'NetgenBlockManagerAdminUIBundle:app:index.html.twig'
        );
    }
}
