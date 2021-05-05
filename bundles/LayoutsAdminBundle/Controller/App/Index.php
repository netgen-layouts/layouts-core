<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\App;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Index extends AbstractController
{
    private string $pageLayout;

    public function __construct(string $pageLayout)
    {
        $this->pageLayout = $pageLayout;
    }

    /**
     * Displays the Netgen Layouts app index page.
     */
    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $appEnvironment = $request->attributes->get('_nglayouts_environment');

        return $this->render(
            $this->pageLayout,
            [
                'debug' => $appEnvironment === 'dev',
            ],
        );
    }
}
