<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App;

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
                sprintf('NetgenBlockManagerAdminBundle:app:index_%s.html.twig', $appEnvironment) :
                'NetgenBlockManagerAdminBundle:app:index.html.twig'
        );
    }

    /**
     * Performs access checks on the controller.
     */
    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_EDITOR');
    }
}
