<?php

namespace Netgen\Bundle\BlockManagerAdminUIBundle\Controller\App;

use Netgen\Bundle\BlockManagerBundle\Controller\Controller;

class IndexController extends Controller
{
    /**
     * Displays the Block Manager app index page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render(
            'NetgenBlockManagerAdminUIBundle:app/index:index.html.twig'
        );
    }
}
