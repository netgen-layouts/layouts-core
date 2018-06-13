<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App;

use Symfony\Component\HttpFoundation\Request;

final class Index extends Controller
{
    /**
     * Displays the Block Manager app index page.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request)
    {
        $appEnvironment = $request->attributes->get('_ngbm_environment');

        return $this->render(
            '@NetgenBlockManagerAdmin/app/index.html.twig',
            [
                'debug' => $appEnvironment === 'dev',
            ]
        );
    }
}
