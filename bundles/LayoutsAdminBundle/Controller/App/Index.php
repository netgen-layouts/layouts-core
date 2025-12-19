<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\App;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Index extends AbstractController
{
    /**
     * Displays the Netgen Layouts app index page.
     */
    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $appEnvironment = $request->attributes->getString('_nglayouts_environment');

        return $this->render(
            '@NetgenLayoutsAdmin/app/pagelayout.html.twig',
            [
                'debug' => $appEnvironment === 'dev',
            ],
        );
    }
}
