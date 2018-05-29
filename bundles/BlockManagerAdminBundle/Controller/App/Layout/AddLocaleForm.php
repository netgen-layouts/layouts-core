<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Layout\Form\AddLocaleType;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\App\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddLocaleForm extends Controller
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
     * Displays and processes form for adding a new locale to layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Layout $layout, Request $request)
    {
        $form = $this->createForm(
            AddLocaleType::class,
            null,
            [
                'layout' => $layout,
                'action' => $this->generateUrl(
                    'ngbm_app_layout_form_add_locale',
                    [
                        'layoutId' => $layout->getId(),
                    ]
                ),
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $locale = $form->get('locale')->getData();
            $sourceLocale = $form->get('sourceLocale')->getData();

            $this->layoutService->addTranslation($layout, $locale, $sourceLocale);

            return new JsonResponse(
                [
                    'locale' => $locale,
                ],
                Response::HTTP_CREATED
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
