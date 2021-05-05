<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\Layouts;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteLayout extends AbstractController
{
    private LayoutService $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Deletes a layout.
     */
    public function __invoke(Request $request, Layout $layout): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:delete');

        if ($request->getMethod() !== Request::METHOD_DELETE) {
            return $this->render(
                '@NetgenLayoutsAdmin/admin/layouts/form/delete_layout.html.twig',
                [
                    'submitted' => false,
                    'error' => false,
                    'layout' => $layout,
                ],
            );
        }

        $this->layoutService->deleteLayout($layout);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
