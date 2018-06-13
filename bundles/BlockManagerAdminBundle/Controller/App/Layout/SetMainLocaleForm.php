<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Layout\Form\SetMainLocaleType;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\App\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetMainLocaleForm extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Displays and processes form for setting the main locale.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke(Layout $layout, Request $request)
    {
        $form = $this->createForm(
            SetMainLocaleType::class,
            null,
            [
                'layout' => $layout,
                'action' => $this->generateUrl(
                    'ngbm_app_layout_form_main_locale',
                    [
                        'layoutId' => $layout->getId(),
                    ]
                ),
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mainLocale = $form->get('mainLocale')->getData();

            if ($mainLocale !== $layout->getMainLocale()) {
                $this->layoutService->setMainTranslation($layout, $mainLocale);
            }

            return new JsonResponse(
                [
                    'main_locale' => $mainLocale,
                ],
                Response::HTTP_OK
            );
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_API,
            [],
            new Response(
                null,
                $form->isSubmitted() ?
                    Response::HTTP_UNPROCESSABLE_ENTITY :
                    Response::HTTP_OK
            )
        );
    }
}
