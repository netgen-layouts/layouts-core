<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\Layouts;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Layout\Form\CopyType;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CopyLayoutForm extends AbstractController
{
    private LayoutService $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Copies a layout.
     */
    public function __invoke(Layout $layout, Request $request): ViewInterface
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:add');

        $copyStruct = $this->layoutService->newLayoutCopyStruct($layout);

        $form = $this->createForm(
            CopyType::class,
            $copyStruct,
            [
                'layout' => $layout,
                'action' => $this->generateUrl(
                    'nglayouts_admin_layouts_layout_copy',
                    [
                        'layoutId' => $layout->getId()->toString(),
                    ],
                ),
            ],
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_ADMIN);
        }

        if ($form->isValid()) {
            $copiedLayout = $this->layoutService->copyLayout($layout, $copyStruct);

            return $this->buildView($copiedLayout, ViewInterface::CONTEXT_ADMIN);
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_ADMIN,
            [],
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY),
        );
    }
}
