<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App;

use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Index extends Controller
{
    /**
     * @var string
     */
    private $pageLayout;

    public function __construct(string $pageLayout)
    {
        $this->pageLayout = $pageLayout;
    }

    /**
     * Displays the Netgen Layouts app index page.
     */
    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:edit');

        $appEnvironment = $request->attributes->get('_ngbm_environment');

        return $this->render(
            $this->pageLayout,
            [
                'debug' => $appEnvironment === 'dev',
            ]
        );
    }
}
