<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Index extends Controller
{
    /**
     * Displays the Netgen Layouts app index page.
     */
    public function __invoke(Request $request): Response
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
