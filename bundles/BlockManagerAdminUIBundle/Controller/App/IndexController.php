<?php

namespace Netgen\Bundle\BlockManagerAdminUIBundle\Controller\App;

use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{
    /**
     * Displays the Block Manager app index page.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $appEnvironment = $request->attributes->get('_ngbm_environment', '');

        return $this->render(
            !empty($appEnvironment) ?
                sprintf(
                    'NetgenBlockManagerAdminUIBundle:app/index:index_%s.html.twig',
                    $appEnvironment
                ) :
            'NetgenBlockManagerAdminUIBundle:app/index:index.html.twig'
        );
    }
}
