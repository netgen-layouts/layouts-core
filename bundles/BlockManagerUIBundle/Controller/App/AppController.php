<?php

namespace Netgen\Bundle\BlockManagerUIBundle\Controller\App;

use Netgen\Bundle\BlockManagerBundle\Controller\Controller;

class AppController extends Controller
{
    public function index()
    {
        return $this->render(
            'NetgenBlockManagerUIBundle:app:index.html.twig'
        );
    }
}
