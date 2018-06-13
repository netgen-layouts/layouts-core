<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Controller;

final class Index extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Displays the index page of layout resolver admin interface.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke()
    {
        return $this->render(
            '@NetgenBlockManagerAdmin/admin/layout_resolver/index.html.twig',
            [
                'rules' => $this->layoutResolverService->loadRules(),
            ]
        );
    }
}
