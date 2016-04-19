<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\BlockManager;

use Netgen\Bundle\BlockManagerBundle\Controller\Controller;

class IndexController extends Controller
{
    /**
     * Displays the index page used to render the block manager app.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render('NetgenBlockManagerBundle:bm:index.html.twig');
    }
}
